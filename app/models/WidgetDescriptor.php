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
    public function widgets() {return $this->hasMany('Widget', 'descriptor_id');}

    /* Returning the specific widgetClass Name
     *
     * @returns string The widget class Name
    */
    public function getClassName() {
        return str_replace(
            '_', '',
            ucwords(str_replace('_',' ', $this->type))
        ) . "Widget";
    }

}
?>