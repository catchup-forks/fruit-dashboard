<?php

/**
* --------------------------------------------------------------------------
* BraintreeDataCollector:
*     Middleware class between Connector and Calculator
* --------------------------------------------------------------------------
*/

class BraintreeDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $connection;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $this->connection = new BraintreeConnector($this->user);
        $this->connection->connect();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * updatePlans
     * --------------------------------------------------
     * Updating the current braintree Plans.
     * @return The braintree plans.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function updatePlans() {
        // Connecting to stripe, and making query.
        try {
            $braintreePlans = Braintree_Plan::all();
        } catch (Exception $e) {
            // Something went wrong.
            return;
        }

        // Getting the plans.
        $plans = [];
        foreach($braintreePlans as $plan) {
            $new_plan = new BraintreePlan(array(
                'plan_id'           => $plan->id,
                'name'              => $plan->name,
                'billing_frequency' => $plan->billingFrequency,
                'price'             => $plan->price,
                'currency'          => $plan->currencyIsoCode,
                'billing_day'        => $plan->billingDayOfMonth,
            ));
            $new_plan->user()->associate($this->user);
            array_push($plans, $new_plan);
        }

        // Delete old, save new.
        foreach (BraintreePlan::where('user_id', $this->user->id)->get() as $stripePlan) {
            BraintreeSubscription::where('plan_id', $stripePlan->id)->delete();
            $stripePlan->delete();
        }

        foreach ($plans as $plan) {
            $plan->save();
        }

        return $plans;
    }

    /**
     * updateSubscriptions
     * --------------------------------------------------
     * Updating the BraintreeSubscriptions.
     * @return The stripe plans.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function updateSubscriptions() {
        // Updating plans to be up to date.
        $this->updatePlans();
        $subscriptions = array();

        // Clollecting subscriptions.
        try {
            $braintreeSubscriptions =  Braintree_Subscription::search(array(
                Braintree_SubscriptionSearch::status()->in(array(
                        Braintree_Subscription::ACTIVE,
                    ))
                )
            );
        } catch (Exception $e) {
            // Something went wrong.
            return;
        }

        foreach ($braintreeSubscriptions as $subscription) {
            $new_subscription = new BraintreeSubscription(array(
                'start'           => $subscription->firstBillingDate,
                'status'          => $subscription->status,
                'subscription_id' => $subscription->id,
                'customer_id'     => $subscription->transactions[0]->customer['id']
            ));
            $plan = BraintreePlan::where('plan_id', $subscription->planId)
                ->first();

            if ($plan === null) {
                // Braintree integrity error, link to a non-existing plan.
                return array();
            }

            $new_subscription->plan()->associate($plan);
            array_push($subscriptions, $new_subscription);
        }

        // Save new.
        foreach ($subscriptions as $subscription) {
            $subscription->save();
        }

        return $subscriptions;
    }

    /**
     * getCustomers
     * --------------------------------------------------
     * Getting a list of customers.
     * @return The braintree customers.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function getCustomers() {
        // Return.
        return Braintree_Customer::all();
    }

    /**
     * getAllSubscriptions
     * --------------------------------------------------
     * Getting a list of all subscriptions.
     * @return The Braintree subscriptions
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function getAllSubscriptions() {
        return  Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::status()->in(array(
                Braintree_Subscription::ACTIVE,
                Braintree_Subscription::EXPIRED,
                Braintree_Subscription::PAST_DUE,
                Braintree_Subscription::PENDING,
                Braintree_Subscription::CANCELED)
                )
            )
        );
    }

    /**
     * getNumberOfCustomers
     * --------------------------------------------------
     * Getting the number of customers.
     * @return The number of customers.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function getNumberOfCustomers($update=False) {
        if ($update) {
            $this->updateSubscriptions();
        }

        $customerIDs = array();
        /* Filtering plans and subscriptions to user. */
        foreach (BraintreePlan::where('user_id', $this->user->id)->get() as $stripePlan) {
            foreach (BraintreeSubscription::where('plan_id', $stripePlan->id)->get() as $subscription) {
                if (!in_array($subscription->customer_id, $customerIDs)) {
                    array_push($customerIDs, $subscription->customer_id);
                }
            }
        }
        return count($customerIDs);
    }
} /* BraintreeDataCollector */
