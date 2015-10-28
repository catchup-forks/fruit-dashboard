<?php

trait StripeWidgetTrait
{
    /**
     * getConnectorClass
     * --------------------------------------------------
     * Returns the connector class for the widgets.
     * @return string
     * --------------------------------------------------
     */
    public function getConnectorClass() {
        return 'StripeConnector';
    }

    /**
     * getServiceSpecificName
     * Returning the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getServiceSpecificName() {
        return 'Stripe - ' . $this->getDescriptor()->name;
    }
}

?>
