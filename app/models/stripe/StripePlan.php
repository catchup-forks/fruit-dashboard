<?php

class StripePlan extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'interval',
        'interval_count',
        'amount',
        'name',
        'livemode',
        'currency',
        'plan_id'
    );

    // -- Relations -- //
    /**
     * Returning the corresponding subscription objects.
     *
     * @return an array with the Subscriptions.
    */
    public function subscriptions() {
        return $this->hasMany('StripeSubscription');
    }
}
