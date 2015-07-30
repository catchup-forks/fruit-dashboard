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
        'ended_at',
        'canceled_at',
        'current_period_start',
        'current_period_end',
        'discount'
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
            $freePlan = Plan::where(['name', 'Free'])->first();

            /* Update the DB */
            $this->plan()->associate($freePlan);
            $this->braintree_subscription_id  = null;
            $this->current_period_start       = Carbon::now();
            $this->current_period_end         = Carbon::now();
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
            
            /* Update braintree customer and payment information */
            $this->braintree_customer_id = $customer->id;
            $this->braintree_payment_method_token = $customer->paymentMethods()[0]->token;
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
                $result['errors'] = TRUE;
                foreach($customerResult->errors->deepAll() AS $error) {
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
        /* Initialize variables */
        $result = ['errors' => FALSE, 'messages' => ''];

        /* The function assumes, that everything is OK to charge the user on Braintree */
        /* Create Braintree subscription */
        $subscriptionResult = Braintree_Subscription::create([
          'paymentMethodToken' => $this->braintree_payment_method_token,
          'planId' => $newPlan->braintree_plan_id
        ]);

        /* Success */
        if ($subscriptionResult->success) {
            Log::info($subscriptionResult);
            /* Change the subscription plan and dates to the new in our DB */
            $this->plan()->associate($newPlan);
            $this->braintree_subscription_id  = $subscriptionResult->subscription->id;
            $this->current_period_start       = Carbon::now();
            $this->current_period_end         = Carbon::now()->addMonth();

            /* Save object */
            $this->save();

        /* Error */
        } else {
            /* Get and store errors */
            $result['errors'] = TRUE;
            foreach($subscriptionResult->errors->deepAll() AS $error) {
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
            $result['errors'] = TRUE;
            foreach($cancellationResult->errors->deepAll() AS $error) {
                $result['messages'] .= $error->code . ": " . $error->message . ' ';
            }
        }

        /* Return result */
        return $result;
    }

}
