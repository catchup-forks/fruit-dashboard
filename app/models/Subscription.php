<?php

class Subscription extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'status',
        'ended_at',
        'canceled_at',
        'current_period_start',
        'current_period_end',
        'discount'
    );

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
    public function plan() { return $this->belongsTo('Plan'); }
}
