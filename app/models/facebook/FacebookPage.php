<?php

class FacebookPage extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'page_id',
        'name',
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
