<?php

class Connection extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'access_token',
        'refresh_token',
        'service'
    );

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
