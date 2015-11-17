<?php

class GoogleAnalyticsProfile extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'profile_id',
        'name',
        'property_id'
    );

    // -- Options -- //
    public $timestamps = false;

    // -- Relations -- //
    public function property() { return $this->belongsTo('GoogleAnalyticsProperty', 'property_id'); }
    public function goals() { return $this->hasMany('GoogleAnalyticsGoal', 'profile_id'); }
}
