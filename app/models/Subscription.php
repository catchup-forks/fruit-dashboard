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
        $freePlan = Plan::where('name', 'Free')->first();
        /* Return */
        return ($this->plan->id == $freePlan->id);
    }

    /**
     * getTrialInfo  
     * --------------------------------------------------
     * @return (array) ($trialInfo) Information about the trial period
     * --------------------------------------------------
     */
    public function getTrialInfo() {
        /* Initialize variables */
        $trialInfo = array();

        /* User is on paid plan */
        if (!$this->isOnFreePlan()) {
           /* Update trialInfo */
            $trialInfo['enabled'] = FALSE;
            
            /* Return trialInfo */
            return $trialInfo;
        }

        /* Trial is not active */
        if (($this->trial_status == 'possible') or 
            ($this->trial_status == 'disabled')) {
            /* Update trialInfo */
            $trialInfo['enabled'] = FALSE;
            
            /* Return trialInfo */
            return $trialInfo;

        /* Trial is active */
        } else {
            /* Handle expired trial */
            if ($this->getDaysRemainingFromTrial() <= 0) {
                /* Update status in db */
                $this->changeTrialState('ended');
            }

            /* Update trialInfo */
            $trialInfo['enabled']       = TRUE;
            $trialInfo['daysRemaining'] = $this->getDaysRemainingFromTrial();
            $trialInfo['endDate']       = $this->getTrialEndDate();
            
            /* Return trialInfo */
            return $trialInfo;
        }
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
                  (($newState != 'ended') or 
                   ($newstate != 'disabled'))) {
            return ;

        /* Enabled state transitions */
        /* Changing from possible to active*/
        } elseif (($this->trial_status == 'possible') and
                  ($newState == 'active')) {
            $this->trial_status = $newState;
            $this->trial_start  = Carbon::now();
            $this->save();

        /* Other transitions */
        } else {
            $this->trial_status = $newState;
            $this->save();
        }
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
        $result = ['errors' => FALSE, 'messages' => ''];

        /* Get customer and update Braintree payment fields */
        $result = $this->getBraintreeCustomer($paymentMethodNonce);

        /* Create new Braintree subscription and update in DB */
        if ($result['errors'] == FALSE) {
            $result = $this->createBraintreeSubscription($newPlan);
        }

        /* If everything went OK, it means that the trial period has ended */
        if ($result['errors'] == FALSE) {
            $this->changeTrialState('disabled');
        }

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
        $result = ['errors' => FALSE, 'messages' => ''];

        /* Get customer and update Braintree payment fields */
        $result = $this->cancelBraintreeSubscription();

        if ($result['errors'] == FALSE) {
            /* Get the free plan */
            $freePlan = Plan::where('name', 'Free')->first();

            /* Update the DB */
            $this->plan()->associate($freePlan);
            $this->braintree_subscription_id  = null;
            $this->changeTrialState('disabled');
            $this->save();
        }

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
        $result = ['errors' => FALSE, 'messages' => ''];

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
                'paymentMethodNonce' => $paymentMethodNonce
            ]);

            /* Update braintree customer and payment information */
            $this->braintree_customer_id = $customer->id;
            $this->braintree_payment_method_token = $paymentMethodResult->paymentMethod->token;
            $this->save();

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
                    $result['errors'] |= TRUE;
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
        $result = ['errors' => FALSE, 'messages' => ''];

        /* Create Braintree subscription */
        $subscriptionResult = Braintree_Subscription::create([
          'paymentMethodToken' => $this->braintree_payment_method_token,
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
                $result['errors'] |= TRUE;
                $result['messages'] .= $error->code . ": " . $error->message . ' ';
            }
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
        $result = ['errors' => FALSE, 'messages' => ''];

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
                    $result['errors'] |= TRUE;
                    $result['messages'] .= $error->code . ": " . $error->message . ' ';
                }
            }
        }

        /* Return result */
        return $result;
    }

    /**
     * getDaysRemainingFromTrial
     * --------------------------------------------------
     * Returns the remaining time from the trial period in days
     * @return (integer) ($daysRemainingFromTrial) The number of days
     * --------------------------------------------------
     */
    private function getDaysRemainingFromTrial() {
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
    private function getTrialEndDate() {
        /* Return the date */
        return Carbon::parse($this->trial_start)->addDays(SiteConstants::getTrialPeriodInDays());
    }


}
