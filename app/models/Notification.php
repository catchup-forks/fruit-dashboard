<?php

class Notification extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
    );

    protected $fillable = array(
        'type',
        'frequency',
        'address',
        'send_minute',
        'send_time',
        'send_weekday',
        'send_day',
        'send_month'
    );

    /* -- No timestamps -- */
    public $timestamps = false; 

    /* -- Relations -- */
    public function user() { return $this->belongsTo('User'); }


    /**
     * ================================================== *
     *                PUBLIC STATIC SECTION               *
     * ================================================== *
     */

     /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */
}
