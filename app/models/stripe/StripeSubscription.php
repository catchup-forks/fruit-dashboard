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

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
    public function plan() { return $this->belongsTo('StripePlan'); }
}
