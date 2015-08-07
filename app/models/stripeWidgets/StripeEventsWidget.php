<?php

class StripeEventsWidget extends Widget implements iAjaxWidget, iCronWidget
{

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
     * getData
     * --------------------------------------------------
     * Returns the events.
     * @param (string) ($selector) Which events are requested.
     * @return (array) ($events) The stripe events.
     * --------------------------------------------------
     */
    public function getData($selector='all') {
        $events = json_decode($this->data->raw_value, 1);
        Log::info($events);
        switch ($selector) {
            case 'all': return $events; break;
            case 'charge': return array_filter($events, function ($e) { return strpos($e['type'], 'charge'); }); break;

            default: break;
        }
        return $events;
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
        if (isset($postData['collect']) && ($postData['collect'])) {
            $this->collectData();
        }
        return $this->getData($postData['type']);
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
            $stripeDataCollector = new StripeDataCollector($this->user());
            foreach ($stripeDataCollector->getEvents() as $event) {
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
