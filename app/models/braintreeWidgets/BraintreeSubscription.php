<?php

class BraintreeSubscription extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'start',
        'status',
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function plan() { return $this->belongsTo('BraintreePlan', 'plan_id'); }
}
