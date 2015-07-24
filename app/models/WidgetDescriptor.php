<?php

class WidgetDescriptor extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'description',
        'type',
        'is_premium',
        'category'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget', 'descriptor_id');}

    /* Returning the specific widgetClass Name
     *
     * @return string The widget class Name
    */
    public function getClassName() {
        return str_replace(
            ' ', '',
            ucwords(str_replace('_',' ', $this->type))
        ) . "Widget";
    }

}
?>