<?php

/**
* -------------------------------------------------------------------------- 
* MixpanelTracker: 
*       Wrapper functions for server-side event tracking    
* Usage:
*       $tracker = new MixpanelTracker();
*       $eventData = array(
*           'en' => 'Event name', // Required.
*           'md' => array(
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
     *     (array)  (md) Custom metadata
     * @return (boolean) (status) True if production server, else false
     * --------------------------------------------------
     */
    public function sendEvent($eventData) {
        if (App::environment('local')) {
            /* Attach user to the event */
            $this->mixpanel->identify(Auth::user()->id);
            
            /* Build and send the request */
            $this->mixpanel->track(
                $eventData['en']
               // array_key_exists('md', $eventData) ? $eventData['md'] : array()
            );
            
            /* Return */
            return true;
        } else {
            /* Return */
            return false;
        }
    }
} /* MixpanelTracker */