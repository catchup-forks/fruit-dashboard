<?php

/**
* --------------------------------------------------------------------------
* StripeDataCollector:
*       Middleware class between Connector and Calculator
* --------------------------------------------------------------------------
*/

class StripeDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $connection;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $this->connection = new StripeConnector($this->user);
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
     * Updating the current stripe Plans.
     * @returns The stripe plans.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function updatePlans() {
        // Connecting to stripe, and making query.
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Plan::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $plans = [];
        foreach($decoded_data['data'] as $plan) {
            $new_plan = new StripePlan(array(
                'plan_id'        => $plan['id'],
                'name'           => $plan['name'],
                'currency'       => $plan['currency'],
                'amount'         => $plan['amount'],
                'interval'       => $plan['interval'],
                'interval_count' => $plan['interval_count']
            ));
            $new_plan->user()->associate($this->user);
            array_push($plans, $new_plan);
        }

        // Delete old, save new.
        foreach (StripePlan::where('user_id', $this->user->id)->get() as $stripePlan) {
            StripeSubscription::where('plan_id', $stripePlan->id)->delete();
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
     * Updating the StripeSubscriptions.
     * @returns The stripe plans.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function updateSubscriptions() {
        // Connecting to stripe.

        // Deleting all subscription to avoid constraints.
        $this->updatePlans();
        $subscriptions = array();

        foreach ($this->getCustomers() as $customer) {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Customer::retrieve($customer['id'])->subscriptions->all()),
                TRUE);
            foreach($decoded_data['data'] as $subscription) {
                $new_subscription = new StripeSubscription(array(
                    'start'       => $subscription['start'],
                    'status'      => $subscription['status'],
                    'customer'    => $subscription['customer'],
                    'ended_at'    => $subscription['ended_at'],
                    'canceled_at' => $subscription['canceled_at'],
                    'quantity'    => $subscription['quantity'],
                    'discount'    => $subscription['discount'],
                    'trial_start' => $subscription['trial_start'],
                    'trial_end'   => $subscription['trial_start'],
                    'discount'    => $subscription['discount']
                ));
                $plan = StripePlan::where('plan_id', $subscription['plan']['id'])->first();
                if ($plan === null) {
                    // Stripe integrity error, link to a non-existing plan.
                    return array();
                }
                $new_subscription->plan()->associate($plan);
                array_push($subscriptions, $new_subscription);
            }
        }

        // Save new.
        foreach ($subscriptions as $subscription) {
            $subscription->save();
        }

        return $subscriptions;
    }

    /**
     * getCustomers
     * Getting a list of customers.
     * --------------------------------------------------
     * @returns The stripe customers.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getCustomers() {
        // Connecting to stripe, and making query.
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Customer::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $customers = [];
        foreach($decoded_data['data'] as $customer) {
            array_push($customers, $customer);
        }

        // Return.
        return $customers;
    }

    /**
     * getEvents
     * Getting events from the last 30 days.
     * --------------------------------------------------
     * @returns The stripe events.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getEvents() {
        // Connecting to stripe, and making query.
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Event::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $events = [];
        foreach($decoded_data['data'] as $event) {
            array_push($events, $event);
        }

        // Return.
        return $events;
    }
    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * loadJSON
     * --------------------------------------------------
     * getting the stripe plans from an already setup stripe connection.
     * @param stripe_json string of the received object.
     * @return the decoded object.
     * --------------------------------------------------
    */
    private function loadJSON($stripe_json) {
        return strstr($stripe_json, '{');
    }

} /* StripeDataCollector */
