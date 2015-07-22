<?php

class Dashboard extends Eloquent
{

    // -- Fields -- //
    protected $fillable = array('name', 'background');
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {
        // Getting the widgets and the descriptors.
        $widgets = DB::table('widgets')->where('dashboard_id', $this->id)->get();
        return Widget::getWidgets($widgets);
    }
    public function user() { return $this->belongsTo('User'); }

}

?>
