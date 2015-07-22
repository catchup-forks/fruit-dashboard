<?php

class Dashboard extends Eloquent
{

    // -- Fields -- //
    protected $fillable = array('name', 'background');
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {
        $widgetCollection = array();

        // Getting the widgets and the descriptors.
        $widgets = DB::table('widgets')->where('dashboard_id', $this->id)->get();
        $descriptors = WidgetDescriptor::lists('type', 'id');

        foreach ($widgets as $widget) {
            $type = $descriptors[$widget->descriptor_id];

            // Creating classname from underscored type.
            $className = str_replace(
                '_', '',
                ucwords(str_replace('_',' ', $type))
            ) . "Widget";

            // Creating new widget.
            $newWidget = new $className(array(
                'position' => $widget->postition,
                'settings' => $widget->settings,
                'state'    => $widget->state
            ));
            $newWidget->descriptor_id = $widget->descriptor_id;
            $newWidget->dashboard_id = $this->id;
            array_push($widgetCollection, $newWidget);
        }

        return $widgetCollection;

    }
    public function user() { return $this->belongsTo('User'); }

}

?>
