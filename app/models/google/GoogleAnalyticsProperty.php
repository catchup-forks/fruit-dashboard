<?php

class GoogleAnalyticsProperty extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'property_id',
        'name',
        'account_id'
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
    public function profiles() { return $this->hasMany('GoogleAnalyticsProfile', 'property_id'); }
}
