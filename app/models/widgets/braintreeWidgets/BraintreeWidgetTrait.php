<?php

trait BraintreeWidgetTrait
{

    /**
     * getConnectorClass
     * --------------------------------------------------
     * Returns the connector class for the widgets.
     * @return string
     * --------------------------------------------------
     */
    public function getConnectorClass() {
        return 'BraintreeConnector';
    }

    /**
     * getServiceSpecificName
     * Returning the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getServiceSpecificName() {
        return 'Braintree - ' . $this->getDescriptor()->name;
    }
}

?>
