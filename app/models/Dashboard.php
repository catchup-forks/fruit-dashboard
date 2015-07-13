<?php

class Dashboard extends Eloquent
{

    // -- Fields -- //
    protected $fillable = array('name', 'background');


    // -- Relations -- //
    /**
     * Returning the corresponding Widget objects.
     *
     * @return an array of general Widget objects.
    */
    public function widgets() {
        return $this->hasMany('Widget');
    }

    /**
     * Returning the corresponding User object.
     *
     * @return a User object.
    */
    public function user() {
        return $this->belongsTo('User');
    }

}

?>
