<?php

/**
* -------------------------------------------------------------------------- 
* MixpanelTracker: 
*       Wrapper functions for server-side event tracking    
* Usage:
*       $tracker = new MixpanelTracker();
*       $eventData = array(
*           'en' => 'Event name', // Required.
*           'meta' => array(
*               'metadata1' => 'value1',
*               'metadata2' => 'value2',
*           ),
*       );
*       $tracker->sendEvent($eventData);
* -------------------------------------------------------------------------- 
*/
class MixpanelTracker {
    /* -- Class properties -- */
    private $mixpanel;

    /* -- Constructor -- */
    public function __construct(){
        $this->mixpanel = Mixpanel::getInstance($_ENV['MIXPANEL_TOKEN']);
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * sendEvent: 
     * --------------------------------------------------
     * Dispatches an event based on the arguments.
     * @param (dict) (eventData) The event data
     *     (string) (en) [Req] Event Name.
     *     (array)  (meta) Custom metadata
     * @return (boolean) (status) True if production server, else false
     * --------------------------------------------------
     */
    public function sendEvent($eventData) {
        if (App::environment('production')) {
            /* Build and send the request */
            $this->mixpanel->track(
                $eventData['en'],
                $eventData['meta']
            );

            error_log($eventData['en']);
            
            /* Return */
            return true;
        } else {
            /* Return */
            return false;
        }
    }
} /* MixpanelTracker */