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

    // -- No timestamps -- //
    public $timestamps = false; 

    // -- Relations -- //
    public function subscriptions() { return $this->hasMany('Subscription'); }
}
