<?php

class BraintreePlan extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'plan_id',
        'billing_frequency',
        'name',
        'price',
        'currency',
        'billing_day'
    );


    // -- Options -- //
    public $timestamps = false;

    // -- Relations -- //
    public function subscriptions() {return $this->hasMany('BraintreeSubscription', 'plan_id'); }
    public function user() { return $this->belongsTo('User'); }
}
