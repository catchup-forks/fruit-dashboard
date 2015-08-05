<?php

/**
* --------------------------------------------------------------------------
* BraintreeCalculator:
*       Wrapper functions for Braintree calculations
* Usage:
*       To retrive $user's mrr: getMrr()
*       To retrive $user's arr: getArr()
* --------------------------------------------------------------------------
*/

class BraintreeCalculator
{
    /* -- Class properties -- */
    private $user;
    private $dataCollector;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $this->dataCollector = new BraintreeDataCollector($this->user);
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getCollector
     * --------------------------------------------------
     * Returning the data collector instance.
     * @return the data collector.
     * --------------------------------------------------
    */
    public function getCollector() {
        return $this->dataCollector;
    }

    /**
     * getMrr
     * --------------------------------------------------
     * Calculating the MRR for the user.
     * @param $update, boolean Whether or not sync the db.
     * @return float The value of the mrr.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function getMrr($update=False) {
        $mrr = 0;

        // Updating database, with the latest data.
        if ($update) {
            $this->dataCollector->updateSubscriptions($update);
        }

        // Iterating through the plans and subscriptions.
        foreach ($this->user->braintreePlans()->get() as $plan) {
            foreach ($plan->subscriptions()->get() as $subscription) {
                // Dealing only with active subscriptions.
                if ($subscription->status == 'active') {
                    $value = $plan->price;
                    $mrr += $value;
                }
            }
        }
        return $mrr;
    }

    /**
     * getArr
     * --------------------------------------------------
     * Calculating the ARR for the user.
     * @param $update, boolean Whether or not sync the db.
     * @return float The value of the arr.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
    */
    public function getArr($update=False) {
        return $this->getMrr($update) * 12;
    }

    /**
     * getArpu
     * --------------------------------------------------
     * Calculating the ARPU for the user.
     * @param $update, boolean Whether or not sync the db.
     * @return float The value of the arpu.
     * @throws StripeNotConnected
     * --------------------------------------------------
    */
    public function getArpu($update=False) {
        $customerNumber = count($this->dataCollector->getCustomers());

        /* Avoiding division by zero. */
        if ($customerNumber == 0) {
            return 0;
        }
        return $this->getArr($update) / $customerNumber;
    }
} /* BraintreeCalculator */
