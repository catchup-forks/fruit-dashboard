<?php

class Dashboard extends Eloquent
{

    // -- Fields -- //
    protected $fillable = array('name', 'background');
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {
        // Getting the widgets and the descriptors.
        return Widget::getWidgets(
               Widget::where('dashboard_id', $this->id)->get());
    }
    public function user() { return $this->belongsTo('User'); }

}

?>
