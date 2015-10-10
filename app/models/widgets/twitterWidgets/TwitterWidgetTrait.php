<?php

trait TwitterWidgetTrait
{

    /**
     * getUser
     * Returning the name of the twitter user.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getUser() {
        $this->user()->twitterUsers()->first()->screen_name;
    }

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
        return '@' . $this->getUser() . ' - ' . $this->descriptor->name;
    }
}

?>
