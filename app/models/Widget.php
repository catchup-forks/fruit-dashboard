<?php

class Widget extends Eloquent
{
    // -- Fields -- //
    protected $guarded = array('state', 'settings', 'position');
    protected $fillable = array('name', 'type', 'is_premium');

    // -- Relations -- //
    /**
     * Returning the corresponding Data objects.
     *
     * @return an array of Data objects.
    */
    public function data() {
        return $this->hasOne('Data');
    }

    /**
     * Returning the corresponding Dashboard object.
     *
     * @return a Dashboard object.
    */
    public function dashboard() {
        return $this->hasOne('Dashboard');
    }

}
?>