<?php

class Connection extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array('token', 'type');

    // -- Relations -- //
    /**
     * Returning the corresponding user object.
     *
     * @return a User object.
    */
    public function user() {
        return $this->belongsTo('User');
    }
}
