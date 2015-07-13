<?php

class ClockWidget extends Widget
{
    // -- Fields -- //
    protected $fillable = array('*');

    // -- Raw DB value transformation -- //
    /**
     * Returning the widget specific settings.
     *
     * @return an array of Widget objects.
    */
    public function getSettings() {
        return array('ampm' => True);
    }

    /**
     * Returning the transformed data of the widget.
     *
     * @return an array with the data.
    */
    public function getData() {
        return null;
    }
}
?>