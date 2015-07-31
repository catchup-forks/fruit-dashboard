<?php

class Dashboard extends Eloquent
{

    // -- Fields -- //
    protected $fillable = array(
        'name',
        'background',
        'number'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget');}
    public function user() { return $this->belongsTo('User'); }

}

?>
