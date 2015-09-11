<?php

class GoogleAnalyticsProperty extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'id',
        'name',
        'account_id'
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
