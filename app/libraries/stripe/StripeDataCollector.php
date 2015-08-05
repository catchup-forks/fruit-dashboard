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
     * @return The stripe plans.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function updatePlans() {
        // Connecting to stripe, and making query.
        try {
            $decodedData = json_decode(
                $this->loadJSON(\Stripe\Plan::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $plans = [];
        foreach($decodedData['data'] as $plan) {
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
     * @return The stripe plans.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function updateSubscriptions() {
        // Connecting to stripe.

        // Deleting all subscription to avoid constraints.
        $this->updatePlans();
        $subscriptions = array();

        foreach ($this->getCustomers() as $customer) {
            $decodedData = json_decode(
                $this->loadJSON(\Stripe\Customer::retrieve($customer['id'])->subscriptions->all()),
                TRUE);
            foreach($decodedData['data'] as $subscription) {
               $new_subscription = new StripeSubscription(array(
                    'subscription_id' => $subscription['id'],
                    'start'           => $subscription['start'],
                    'status'          => $subscription['status'],
                    'customer'        => $subscription['customer'],
                    'ended_at'        => $subscription['ended_at'],
                    'canceled_at'     => $subscription['canceled_at'],
                    'quantity'        => $subscription['quantity'],
                    'discount'        => $subscription['discount'],
                    'trial_start'     => $subscription['trial_start'],
                    'trial_end'       => $subscription['trial_start'],
                    'discount'        => $subscription['discount']
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
     * getNumberOfCustomers
     * --------------------------------------------------
     * Getting the number of customers.
     * @return The number of customers.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getNumberOfCustomers($update=False) {
        if ($update) {
            $this->updateSubscriptions();
        }

        $customerIDs = array();
        /* Filtering plans and subscriptions to user. */
        foreach (StripePlan::where('user_id', $this->user->id)->get() as $stripePlan) {
            foreach (StripeSubscription::where('plan_id', $stripePlan->id)->get() as $subscription) {
                if (!in_array($subscription->customer, $customerIDs)) {
                    array_push($customerIDs, $subscription->customer);
                }
            }
        }
        return count($customerIDs);
    }

    /**
     * getCustomers
     * Getting a list of customers.
     * --------------------------------------------------
     * @return The stripe customers.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getCustomers() {
        $rawData = array();
        $decodedData = array();
        $hasMore = TRUE;
        $startingAfter = null;

        while ($hasMore) {
            try {
                /* Collecting events with pagination. */
                if ($startingAfter) {
                    $rawData = \Stripe\Customer::all(array(
                        "limit"          => 100,
                        "starting_after" => $startingAfter
                    ));
                } else {
                    $rawData = \Stripe\Customer::all(array("limit" => 100));
                }
                /* Adding objects to collection. */
                $currentData = json_decode($this->loadJSON($rawData), TRUE);
                $decodedData = array_merge($decodedData, $currentData['data']);

            } catch (\Stripe\Error\Authentication $e) {
                // Access token expired. Calling handler.
                $this->getNewAccessToken();
            }
            $hasMore = $currentData['has_more'];
            $startingAfter = end($currentData['data'])['id'];
        }

        // Getting the plans.
        $customers = [];
        foreach($decodedData as $customer) {
            array_push($customers, $customer);
        }

        // Return.
        return $customers;
    }

    /**
     * getEvents
     * Getting events from the last 30 days.
     * --------------------------------------------------
     * @return The stripe events.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getEvents() {
        /* Connecting to stripe, and making query. */
        $rawData = array();
        $decodedData = array();
        $hasMore = TRUE;
        $startingAfter = null;

        while ($hasMore) {
            try {
                /* Collecting events with pagination. */
                if ($startingAfter) {
                    $rawData = \Stripe\Event::all(array(
                        "limit"          => 100,
                        "starting_after" => $startingAfter
                    ));
                } else {
                    $rawData = \Stripe\Event::all(array("limit" => 100));
                }
                /* Adding objects to collection. */
                $currentData = json_decode($this->loadJSON($rawData), TRUE);
                $decodedData = array_merge($decodedData, $currentData['data']);

            } catch (\Stripe\Error\Authentication $e) {
                // Access token expired. Calling handler.
                $this->getNewAccessToken();
            }
            $hasMore = $currentData['has_more'];
            $startingAfter = end($currentData['data'])['id'];
        }

        // Getting the plans.
        $events = [];
        foreach($decodedData as $event) {
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
