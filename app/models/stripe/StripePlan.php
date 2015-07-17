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


    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function subscriptions() {return $this->hasMany('StripeSubscription'); }
    public function user() { return $this->belongsTo('User'); }
}
