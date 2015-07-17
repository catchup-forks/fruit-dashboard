<?php

class Plan extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'interval',
        'interval_count',
        'amount',
        'name',
        'description'
    );

    // -- Relations -- //
    public function subscriptions() { return $this->hasMany('Subscription'); }
}
