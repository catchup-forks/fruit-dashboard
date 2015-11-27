<?php

class Notification extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
    );

    protected $fillable = array(
        'type',
        'frequency',
        'address',
        'send_minute',
        'send_time',
        'send_weekday',
        'send_day',
        'send_month',
        'selected_widgets',
        'is_enabled'
    );

    /* -- No timestamps -- */
    public $timestamps = false;

    /* -- Relations -- */
    public function user() { return $this->belongsTo('User'); }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * newFromBuilder
     * Override the base Model function to use polymorphism.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    public function newFromBuilder($attributes=array()) {
        $className = ucfirst($attributes->type).'Notification';
        $instance = new $className;
        $instance->exists = true;
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;
    }

    /**
     * getSelectedWidgets
     * Returns an array of the selected widgets.
     * --------------------------------------------------
     * @return {array} $selectedWidgets
     * --------------------------------------------------
     */
    public function getSelectedWidgets() {
        if (is_array($this->selected_widgets)) {
            return $this->selected_widgets;
        } else if (is_string($this->selected_widgets)) {
            $decoded = json_decode($this->selected_widgets);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return array();
    }

    /**
     * addToSelectedWidgets
     * Adds the given widgets to the selected widgets
     * --------------------------------------------------
     * @param {eloquent query or array with Widgets} $widgets | The widgets
     * @return {true}
     * --------------------------------------------------
     */
    public function addToSelectedWidgets($widgets) {
        /* Get already selected widgets */
        $widgetIDs = $this->getSelectedWidgets();

        /* Iterate through the new widgets */
        foreach ($widgets as $widget) {
            array_push($widgetIDs, $widget->id);
        }
        /* Make list unique */
        $widgetIDs = array_unique($widgetIDs);

        /* Encode list to json */
        $widgetIDs = json_encode($widgetIDs);

        /* Save list*/
        $this->selected_widgets = $widgetIDs;
        $this->save();
    }

    /**
     * removeFromSelectedWidgets
     * Adds the given widgets to the selected widgets
     * --------------------------------------------------
     * @param {eloquent query or array with Widgets} $widgets | The widgets
     * @return {true}
     * --------------------------------------------------
     */
    public function removeFromSelectedWidgets($widgets) {
        /* Get already selected widgets */
        $widgetIDs = $this->getSelectedWidgets();

        /* Iterate through the new widgets */
        foreach ($widgets as $widget) {
            $widgetIDs = array_diff($widgetIDs, array($widget->id));
        }
        /* Make list unique */
        $widgetIDs = array_unique($widgetIDs);

        /* Encode list to json */
        $widgetIDs = json_encode($widgetIDs);

        /* Save list*/
        $this->selected_widgets = $widgetIDs;
        $this->save();
    }
}
