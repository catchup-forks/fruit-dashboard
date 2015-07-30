<?php

class StripeEventsWidget extends Widget implements iAjaxWidget, iCronWidget
{

    public function collectData() {
        $currentData = $this->getHistogram();
        try {
            $stripeCalculator = new StripeCalculator($this->user());
        } catch (StripeNotConnected $e) {
            ;
        }
        array_push($currentData, $stripeCalculator->getMrr(TRUE));
        $this->data->raw_value = json_encode($currentData);
        $this->data->save();
        $this->checkIntegrity();
    }

    /**
     * createDataScheme
     * --------------------------------------------------
     * Returning a deafult scheme for the data.
     * @return string json encoded empty array.
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array());
    }

    /**
     * handleAjax
     * --------------------------------------------------
     * Handling ajax request, updating events dinamically.
     * @param $postData the data from the request.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
        $this->collectData();
        return $this->getData();
    }

    /**
     * collectData
     * --------------------------------------------------
     * Running dataCollector.
     * --------------------------------------------------
    */
    public function collectData() {
        $events = array();
        try {
            $stripeCalculator = new StripeCalculator($this->user());
            foreach ($stripeCalculator->getEvents() as $event) {
                array_push($events, $event);
            }
            $this->data->raw_value = json_encode($events);
            $this->data->save();
            $this->checkIntegrity();
        } catch (StripeNotConnected $e) {
            ;
        }

    }
}
?>
