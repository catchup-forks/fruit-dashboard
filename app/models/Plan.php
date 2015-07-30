<?php

class Plan extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
    );

    protected $fillable = array(
        'interval',
        'interval_count',
        'amount',
        'name',
        'description',
        'braintree_plan_id',
    );

    /* -- No timestamps -- */
    public $timestamps = false; 

    /* -- Relations -- */
    public function subscriptions() { return $this->hasMany('Subscription'); }
}
