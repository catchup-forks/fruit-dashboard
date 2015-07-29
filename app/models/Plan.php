<?php

class Plan extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
        'plan_id',
    );

    protected $fillable = array(
        'interval',
        'interval_count',
        'amount',
        'name',
        'description'
    );

    /* -- No timestamps -- */
    public $timestamps = false; 

    /* -- Relations -- */
    public function subscriptions() { return $this->hasMany('Subscription'); }
}
