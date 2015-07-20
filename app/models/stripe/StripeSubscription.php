<?php

class StripeSubscription extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'start',
        'status',
        'customer',
        'ended_at',
        'canceled_at',
        'quantity',
        'discount',
        'trial_start',
        'trial_end'
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
<<<<<<< HEAD
    public function plan() { return $this->belongsTo('StripePlan'); }
=======
    public function plan() { return $this->belongsTo('StripePlan', 'plan_id'); }
>>>>>>> 9d904e9ec6e7764ce5969b69b2e90e3c33b7b7c4
}
