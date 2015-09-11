<?php

class BraintreeSubscription extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'start',
        'status',
        'subscription_id',
        'customer_id'
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function plan() { return $this->belongsTo('BraintreePlan', 'plan_id'); }
}
