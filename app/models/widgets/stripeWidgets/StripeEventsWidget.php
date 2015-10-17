<?php

class StripeEventsWidget extends DataWidget implements iDataWidget
{

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
        if ($selector == 'all') {
            return array_slice($events, 0 , 10);
        }
        $filteredEvents = array_filter(
            $events,
            function ($e) use ($selector) {
                if (strpos($e['type'], $selector) !== FALSE) {
                    return TRUE;
                }
                return FALSE;
            }
        );
        return array_values($filteredEvents);
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
            $this->collect();
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
