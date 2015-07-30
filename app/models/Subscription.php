<?php

class Subscription extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
        'braintree_customer_id',
        'braintree_payment_method_token',
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
     * commitSubscribe
     * --------------------------------------------------
     * @param (string)  ($paymentMethodNonce) The authorization token for the payment
     * @param (Plan)    ($newPlan)            The new plan
     * @return Creates a Braintree Subscription and charges the user
     * --------------------------------------------------
     */
    public function commitSubscribe($paymentMethodNonce, $newPlan) {
        /* Get customer and update Braintree payment fields */
        $result = $this->getAndUpdateCustomer($paymentMethodNonce);

        if ($result['errors'] == FALSE) {
            /* Create new Braintree subscription and update in DB */
            $result = $this->createAndUpdateSubscription($newPlan);
            if ($result['errors'] == FALSE) {
                /* Return with success */
                return ['errors' => False, 'messages' => ''];
            } else {
                /* Return with errors */
                return ['errors' => TRUE, 'messages' => $result['messages']];
            }
        } else {
            /* Return with errors */
            return ['errors' => TRUE, 'messages' => $result['messages']];
        }
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * getAndUpdateCustomer
     * --------------------------------------------------
     * @param (string) ($paymentMethodNonce) The authorization token for the payment
     * @return Creates a Braintree Subscription, from this object.
     * --------------------------------------------------
     */
    private function getAndUpdateCustomer($paymentMethodNonce) {
        /* Initialize variables */
        $result = [
            'errors' => FALSE, 
            'messages' => '',
        ];

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
                    $result['messages'] .= $error->code . ": " . $error->message . "\n";
                }
            }

            /* Return result */
            return $result;
        }
    }

    /**
     * createAndUpdateSubscription
     * --------------------------------------------------
     * @param (Plan) ($newPlan) The new plan
     * @return Creates a Braintree Subscription, from this object.
     * --------------------------------------------------
     */
    private function createAndUpdateSubscription($newPlan) {
        /* Initialize variables */
        $result = [
            'errors' => FALSE, 
            'messages' => '',
        ];

        /* The function assumes, that everything is OK to charge the user on Braintree */
        /* Create Braintree subscription */
        $subscriptionResult = Braintree_Subscription::create([
          'paymentMethodToken' => $this->braintree_payment_method_token,
          'planId' => $newPlan->braintree_plan_id
        ]);

        /* Success */
        if ($subscriptionResult->success) {
            /* Change the subscription plan and dates to the new in our DB */
            $this->plan()->associate($newPlan);
            $this->current_period_start = Carbon::now();
            $this->current_period_end = Carbon::now()->addMonth();

            /* Save object */
            $this->save();

        /* Error */
        } else {
            /* Get and store errors */
            $result['errors'] = TRUE;
            foreach($subscriptionResult->errors->deepAll() AS $error) {
                $result['messages'] .= $error->code . ": " . $error->message . "\n";
            }
        }

        /* Return result */
        return $result;
    }
}
