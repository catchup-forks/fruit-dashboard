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
            $this->dataCollector->updateSubscriptions();
        }

        // Iterating through the plans and subscriptions.
        foreach ($this->user->brainrteePlans as $plan) {
            foreach ($plan->subscriptions as $subscription) {
                // Dealing only with active subscriptions.
                if ($subscription->status == 'active') {
                    //
                    $value = $plan->price * (1 / $subscription->billing_frequency);
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

} /* BraintreeCalculator */
