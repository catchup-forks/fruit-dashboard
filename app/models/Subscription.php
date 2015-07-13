<?php

class Subscription extends Eloquent
{
    // -- Fields -- //
    protected $guarded = array('status', 'ended_at', 'canceled_at', 'current_period_start', 'current_period_end', 'discount');

    // -- Relations -- //
    /**
     * Returning the corresponding user object.
     *
     * @return a User object.
    */
    public function user() {
        return $this->belongsTo('User');
    }
    /**
     * Returning the corresponding plan object.
     *
     * @return a Plan object.
    */
    public function plan() {
        return $this->belongsTo('Plan');
    }
}
