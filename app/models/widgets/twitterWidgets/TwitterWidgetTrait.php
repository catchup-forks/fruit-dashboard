<?php

trait TwitterWidgetTrait
{

    /**
     * getUser
     * Return the name of the twitter user.
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
     * getServiceSpecificName
     * Return the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getServiceSpecificName() {
        return '@' . $this->getUser();
    }
}

?>
