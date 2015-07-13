<?php

class Plan extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array('interval', 'interval_count', 'amount', 'name', 'description');

    // -- Relations -- //
    /**
     * Returning the corresponding subscription objects.
     *
     * @return an array with the Subscriptions.
    */
    public function subscriptions() {
        return $this->hasMany('Subscription');
    }
}
