<?php

trait TwitterWidgetTrait
{
    /**
     * getConnectorClass
     * --------------------------------------------------
     * Returns the connector class for the widgets.
     * @return string
     * --------------------------------------------------
     */
    public function getConnectorClass() {
        return 'TwitterConnector';
    }

    /**
     * getDefaultName
     * Returning the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getDefaultName() {
        return 'Twitter - ' . $this->descriptor->name;
    }
}

?>
