<?php

class Subscription extends Eloquent
{
    /* -- Fields -- */
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
     * commit
     * --------------------------------------------------
     * Creating a braintree subscription, from this object.
     * --------------------------------------------------
     */
    public function commit($paymentMethodNonce) {
        /* Braintree plan must be associated with the plan. */
        if (is_null($this->plan->plan_id)) {
            return ;
        }

        /* Avoiding adding duplicates. */
        foreach ($this->user->subscriptions as $subscription) {
            $plan_id = $subscription->plan->plan_id;
            if (!is_null($plan_id) && $plan_id == $this->plan->plan_id) {
                throw new AlreadySubscribed();
                  ;
            }
        }

        $newSubscription = Braintree_Subscription::create(array(
            'planId'             => $this->plan->plan_id,
            'paymentMethodNonce' => $paymentMethodNonce
        ));



    }
}
