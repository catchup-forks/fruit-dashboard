<?php

class Subscription extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
        'braintree_customer_id',
        'braintree_payment_method_token',
        'braintree_subscription_id'
    );

    protected $fillable = array(
        'status',
        'trial_status',
        'trial_start',
    );

    /* -- Relations -- */
    public function user() { return $this->belongsTo('User'); }
    public function plan() { return $this->belongsTo('Plan'); }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * isOnFreePlan
     * --------------------------------------------------
     * @return (array) ($trialInfo) Information about the trial period
     * --------------------------------------------------
     */
    public function isOnFreePlan() {
        /* Get the free plan */
        $freePlan = Plan::getFreePlan();
        /* Return */
        return ($this->plan->id == $freePlan->id);
    }


    /**
     * getSubscriptionInfo
     * --------------------------------------------------
     * @return (array) ($subscriptionInfo) Information about the subscription
     * --------------------------------------------------
     * State Matrix legend:
     *     TS: Trial period state       (possible | active | ended | disabled)
     *     TD: Trial badge displayed    (true | false)
     *     PE: Premium feature enabled  (true | false)
     * --------------------------------------------------------------------
     * Cases @param SUBSCRIPTION_MODE:
     *     premium_feature_and_trial     |     TS     |   TD    |   PE    |
     * --------------------------------------------------------------------
     * 1) No premium functionality added |  possible  |  false  |  false  |
     * 2) Premium functionality added,
     *    Remaining days > 0             |   active   |  true   |  true   |
     * 3) Premium functionality added,
     *    Remaining days <= 0            |   ended    |  true   |  false  |
     * 4) Unsubscribed from Premium plan |  disabled  |  false  |  false  |
     * 5) Subscribed to Premium plan     |  disabled  |  false  |  true   |
     * --------------------------------------------------------------------
     * Cases @param SUBSCRIPTION_MODE:
     *     premium_feature_only          |     TS     |   TD    |   PE    |
     * --------------------------------------------------------------------
     * 1) Is on free plan                |  disabled  |  false  |  false  |
     * 2) Subscribed to Premium plan     |  disabled  |  false  |  true   |
     * --------------------------------------------------------------------
     * Cases @param SUBSCRIPTION_MODE
     *     trial_only                    |     TS     |   TD    |   PE    |
     * --------------------------------------------------------------------
     * 1) Remaining days > 0             |   active   |  true   |  true   |
     * 2) Remaining days <= 0            |   ended    |  true   |  false  |
     * 3) Unsubscribed from Premium plan |  disabled  |  false  |  false  |
     * 4) Subscribed to Premium plan     |  disabled  |  false  |  true   |
     * --------------------------------------------------------------------
     */
    public function getSubscriptionInfo() {
        /* premium_feature_and_trial */
        if ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_and_trial') {
            /* Handle just expired trial */
            if (($this->trial_status == 'active') and
                ($this->getDaysRemainingFromTrial() <= 0)) {

                /* Update status in db */
                $this->changeTrialState('ended');

                /* Update dashboard cache */
                $this->user->updateDashboardCache();

                /* Track event | TRIAL ENDED */
                $tracker = new GlobalTracker();
                $tracker->trackAll('lazy', array(
                    'en' => 'Trial ended',
                    'el' => $this->user->email)
                );
            }

            /* Return subscriptionInfo based on the cases */
            if ($this->isOnFreePlan()) {
                /* ==== CASE 1 === */
                if ($this->trial_status == 'possible') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'possible',
                        'TD' => false,
                        'PE' => false,
                    ];

                /* ==== CASE 2 === */
                } elseif ($this->trial_status == 'active') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'active',
                        'TD' => true,
                        'trialDaysRemaining' => $this->getDaysRemainingFromTrial(),
                        'trialEndDate'       => $this->getTrialEndDate(),
                        'PE' => true,
                    ];

                /* ==== CASE 3 === */
                } elseif ($this->trial_status == 'ended') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'ended',
                        'TD' => true,
                        'trialDaysRemaining' => $this->getDaysRemainingFromTrial(),
                        'trialEndDate' => $this->getTrialEndDate(),
                        'PE' => false,
                    ];

                /* ==== CASE 4 === */
                } elseif ($this->trial_status == 'disabled') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'disabled',
                        'TD' => false,
                        'PE' => false,
                    ];
                }

            /* ==== CASE 5 === */
            } else {
                /* Build and return subscriptionInfo */
                return [
                    'TS' => 'disabled',
                    'TD' => false,
                    'PE' => true,
                ];
            }
        /* premium_feature_only */
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_only') {
            /* Return subscriptionInfo based on the cases */
            /* ==== CASE 1 === */
            if ($this->isOnFreePlan()) {
                /* Build and return subscriptionInfo */
                return [
                    'TS' => 'disabled',
                    'TD' => false,
                    'PE' => false,
                ];

            /* ==== CASE 2 === */
            } else {
                /* Build and return subscriptionInfo */
                return [
                    'TS' => 'disabled',
                    'TD' => false,
                    'PE' => true,
                ];
            }
        /* trial_only */
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'trial_only') {
            /* Handle just expired trial */
            if (($this->trial_status == 'active') and
                ($this->getDaysRemainingFromTrial() <= 0)) {

                /* Update status in db */
                $this->changeTrialState('ended');

                /* Update dashboard cache */
                $this->user->updateDashboardCache();

                /* Track event | TRIAL ENDED */
                $tracker = new GlobalTracker();
                $tracker->trackAll('lazy', array(
                    'en' => 'Trial ended',
                    'el' => $this->user->email)
                );
            }

            /* Return subscriptionInfo based on the cases */
            if ($this->isOnFreePlan()) {
                /* ==== CASE 1 === */
                if ($this->trial_status == 'active') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'active',
                        'TD' => true,
                        'trialDaysRemaining' => $this->getDaysRemainingFromTrial(),
                        'trialEndDate'       => $this->getTrialEndDate(),
                        'PE' => true,
                    ];

                /* ==== CASE 2 === */
                } elseif ($this->trial_status == 'ended') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'ended',
                        'TD' => true,
                        'trialDaysRemaining' => $this->getDaysRemainingFromTrial(),
                        'trialEndDate' => $this->getTrialEndDate(),
                        'PE' => false,
                    ];

                /* ==== CASE 3 === */
                } elseif ($this->trial_status == 'disabled') {
                    /* Build and return subscriptionInfo */
                    return [
                        'TS' => 'disabled',
                        'TD' => false,
                        'PE' => false,
                    ];
                }

            /* ==== CASE 4 === */
            } else {
                /* Build and return subscriptionInfo */
                return [
                    'TS' => 'disabled',
                    'TD' => false,
                    'PE' => true,
                ];
            }
        } /* SUBSCRIPTION_MODE  */
    }

    /**
     * changeTrialState
     * --------------------------------------------------
     * @param (string) ($newState)
     * @return Changes the trial state to the provided
     * --------------------------------------------------
     */
    public function changeTrialState($newState) {
        /* The disabled state cannot be changed */
        if ($this->trial_status == 'disabled') {
            return ;

        /* The ended state can be changed to disabled only */
        } elseif (($this->trial_status == 'ended') and
                  ($newState != 'disabled')) {
            return ;

        /* The active state can be changed only to ended and disabled */
        } elseif (($this->trial_status == 'active') and
                  (($newState != 'ended') and
                   ($newState != 'disabled'))) {
            return ;

        /* Enabled state transitions */
        /* Changing from possible to active --> TRIAL STARTS */
        } elseif (($this->trial_status == 'possible') and
                  ($newState == 'active')) {
            /* Change the state */
            $this->trial_status = $newState;
            $this->trial_start  = Carbon::now();
            $this->save();

            /* Track event | TRIAL STARTS */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Trial starts',
                'el' => $this->user->email)
            );

        /* Other transitions */
        } else {
            /* Change the state */
            $this->trial_status = $newState;
            $this->save();
        }
    }


    /**
     * getDaysRemainingFromTrial
     * --------------------------------------------------
     * Returns the remaining time from the trial period in days
     * @return (integer) ($daysRemainingFromTrial) The number of days
     * --------------------------------------------------
     */
    public function getDaysRemainingFromTrial() {
        /* Get the difference */
        $diff = Carbon::now()->diffInDays(Carbon::parse($this->trial_start));

        /* Return the diff in days or 0 if trial has ended */
        if ($diff <= SiteConstants::getTrialPeriodInDays() ) {
            return SiteConstants::getTrialPeriodInDays()-$diff;
        } else {
            return 0;
        }
    }


    /**
     * getTrialEndDate
     * --------------------------------------------------
     * Returns the trial period ending date
     * @return (date) ($trialEndDate) The ending date
     * --------------------------------------------------
     */
    public function getTrialEndDate() {
        /* Return the date */
        return Carbon::parse($this->trial_start)->addDays(SiteConstants::getTrialPeriodInDays());
    }


    /**
     * createSubscription
     * --------------------------------------------------
     * @param (string)  ($paymentMethodNonce) The authorization token for the payment
     * @param (Plan)    ($newPlan)            The new plan
     * @return Creates a Braintree Subscription and charges the user
     * --------------------------------------------------
     */
    public function createSubscription($paymentMethodNonce, $newPlan) {
        /* Initialize variables */
        $result = ['errors' => false, 'messages' => ''];

        /* Get customer and update Braintree payment fields */
        $result = $this->getBraintreeCustomer($paymentMethodNonce);

        /* Create new Braintree subscription and update in DB */
        if ($result['errors'] == false) {
            $result = $this->createBraintreeSubscription($newPlan);
        }

        /* If everything went OK, it means that the trial period has ended */
        if ($result['errors'] == false) {
            $this->changeTrialState('disabled');
        }

        /* Update dashboard cache */
        $this->user->updateDashboardCache();

        /* Return the updated result */
        return $result;
    }

    /**
     * cancelSubscription
     * --------------------------------------------------
     * @return Cancels the subscription for the user
     * --------------------------------------------------
     */
    public function cancelSubscription() {
        /* Initialize variables */
        $result = ['errors' => false, 'messages' => ''];

        /* Get customer and update Braintree payment fields */
        $result = $this->cancelBraintreeSubscription();

        if ($result['errors'] == false) {
            /* Get the free plan */
            $freePlan = Plan::getFreePlan();

            /* Update the DB */
            $this->plan()->associate($freePlan);
            $this->braintree_subscription_id  = null;
            $this->changeTrialState('disabled');
            $this->save();
        }

        /* Update dashboard cache */
        $this->user->updateDashboardCache();

        /* Return the updated result */
        return $result;
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * getBraintreeCustomer
     * --------------------------------------------------
     * @param (string) ($paymentMethodNonce) The authorization token for the payment
     * @return Creates a Braintree Subscription, from this object.
     * --------------------------------------------------
     */
    private function getBraintreeCustomer($paymentMethodNonce) {
        /* Initialize variables */
        $result = ['errors' => false, 'messages' => ''];

        /* Get or create the Braintree customer */
        /* Get existing customer */
        try {
            /* Customer ID is not set, proceed with create */
            if ($this->braintree_customer_id == null) {
                throw new Braintree_Exception_NotFound;
            }

            /* Get the customer */
            $customer = Braintree_Customer::find($this->braintree_customer_id);

            /* Create new paymentmethod with the current data */
            $paymentMethodResult = Braintree_PaymentMethod::create([
                'customerId' => $customer->id,
                'paymentMethodNonce' => $paymentMethodNonce,
                'options' => [
                    'verifyCard' => true
                ]
            ]);

            /* Card verification failed */
            if (!$paymentMethodResult->success) {
                $verification = $paymentMethodResult->creditCardVerification;
                $result['errors'] |= true;
                $result['messages'] .= 'Sorry, your credit card cannot be verified. ' . $verification->processorResponseText;
            } else {
                /* Update braintree customer and payment information */
                $this->braintree_customer_id = $customer->id;
                $this->braintree_payment_method_token = $paymentMethodResult->paymentMethod->token;
                $this->save();
            }

            /* Return result */
            return $result;

        /* No customer found with the ID, create a new */
        } catch (Braintree_Exception_NotFound $e) {
            /* Create new customer */
            $customerResult = Braintree_Customer::create([
                'firstName' => $this->user->name,
                'email'     => $this->user->email,
                'paymentMethodNonce' => $paymentMethodNonce,
            ]);

            /* Success */
            if ($customerResult->success) {
                /* Store braintree customer and payment information */
                $this->braintree_customer_id = $customerResult->customer->id;
                $this->braintree_payment_method_token = $customerResult->customer->paymentMethods()[0]->token;
                $this->save();

            /* Error */
            } else {
                /* Get and store errors */
                foreach($customerResult->errors->deepAll() AS $error) {
                    $result['errors'] |= true;
                    $result['messages'] .= $error->code . ": " . $error->message . ' ';
                }
            }

            /* Return result */
            return $result;
        }
    }

    /**
     * createBraintreeSubscription
     * --------------------------------------------------
     * @param (Plan) ($newPlan) The new plan
     * @return Creates a Braintree Subscription, from this object.
     * --------------------------------------------------
     */
    private function createBraintreeSubscription($newPlan) {
        /* The function assumes, that everything is OK to charge the user on Braintree */

        /* Initialize variables */
        $result = ['errors' => false, 'messages' => ''];

        /* Create Braintree subscription */
        $subscriptionResult = Braintree_Subscription::create([
          'paymentMethodToken' => $this->braintree_payment_method_token,
          'merchantAccountId' => $newPlan->braintree_merchant_account_id,
          'planId' => $newPlan->braintree_plan_id
        ]);

        /* Success */
        if ($subscriptionResult->success) {
            /* Change the subscription plan */
            $this->plan()->associate($newPlan);
            $this->braintree_subscription_id  = $subscriptionResult->subscription->id;

            /* Save object */
            $this->save();

        /* Error */
        } else {
            /* Get and store errors */
            foreach($subscriptionResult->errors->deepAll() AS $error) {
                $result['errors'] |= true;
                $result['messages'] .= $error->code . ": " . $error->message . ' ';
            }
            Log::error($subscriptionResult->transaction->status);
        }

        /* Return result */
        return $result;
    }

    /**
     * cancelBraintreeSubscription
     * --------------------------------------------------
     * @return Cancels the current Braintree Subscription.
     * --------------------------------------------------
     */
    private function cancelBraintreeSubscription() {
        /* Initialize variables */
        $result = ['errors' => false, 'messages' => ''];

        /* Cancel braintree subscription */
        $cancellationResult = Braintree_Subscription::cancel($this->braintree_subscription_id);

        /* Error */
        if (!$cancellationResult->success) {
            /* Get and store errors */
            foreach($cancellationResult->errors->deepAll() AS $error) {
                /* SKIP | Subscription has already been canceled. */
                if ($error->code == SiteConstants::getBraintreeErrorCodes()['Subscription has already been canceled']) {
                    continue;
                } else {
                    $result['errors'] |= true;
                    $result['messages'] .= $error->code . ": " . $error->message . ' ';
                }
            }
        }

        /* Return result */
        return $result;
    }
}
