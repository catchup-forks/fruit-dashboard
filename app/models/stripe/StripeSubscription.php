<?php

class StripeSubscription extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'start',
        'status',
        'customer',
        'ended_at',
        'canceled_at',
        'quantity',
        'discount',
        'trial_start',
        'trial_end'
    );


    // -- Relations -- //
    /**
     * Returning the corresponding user object.
     *
     * @return a User object.
    */
    public function user() {
        return $this->belongsTo('User');
    }
    /**
     * Returning the corresponding stripeplan object.
     *
     * @return a StripePlan object.
    */
    public function plan() {
        return $this->belongsTo('StripePlan');
    }
}
