<?php

class WidgetDescriptor extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'description',
        'type',
        'is_premium'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {
        return Widget::getWidgets(
               Widget::where('descriptor_id', $this->id));
    }

}
?>