<?php

class TwitterUser extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'screen_name',
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
