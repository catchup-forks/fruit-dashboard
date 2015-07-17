<?php

class Widget extends Eloquent
{
    // -- Fields -- //
    protected $guarded = array(
        'state',
        'settings',
        'position'
    );
    protected $fillable = array(
        'name',
        'description',
        'type',
        'is_premium'
    );

    // -- Relations -- //
    public function data() { return $this->hasOne('Data'); }
    public function dashboard() { return $this->hasOne('Dashboard'); }

}
?>