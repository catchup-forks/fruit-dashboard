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
    private static $mixpanel;

    /* -- Constructor -- */
    public function __construct(){
        self::$mixpanel = Mixpanel::getInstance($_ENV['MIXPANEL_TOKEN']);
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
     * @return (boolean) true
     * --------------------------------------------------
     */
    public function sendEvent($eventData) {
        try {
            /* Attach user to the event */
            if (Auth::check()) {
                self::$mixpanel->identify(Auth::user()->id);
            }

            /* Build and send the request */
            self::$mixpanel->track(
                $eventData['en']
               // array_key_exists('md', $eventData) ? $eventData['md'] : array()
            );
        } catch (Exception $e) {
            Log::info('MixpanelTracker exception');
            Log::info($e);
        }

        /* Return */
        return true;
    }
} /* MixpanelTracker */