<?php

/**
* --------------------------------------------------------------------------
* KissmetricsTracker:
*       Wrapper functions for server-side event tracking
* Usage:
*       $tracker = new KissmetricsTracker();
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
class KissmetricsTracker {
    /* -- Class properties -- */
    private static $api_key;

    /* -- Constructor -- */
    public function __construct(){
        self::$api_key = $_ENV['KISSMETRICS_KEY'];
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
            /* Initialize client */
            $km = new KISSmetrics\Client(self::$api_key, KISSmetrics\Transport\Sockets::initDefault()); 

            /* Identify user and build data */
            $km->identify(Auth::user()->email)->record($eventData['en'], $eventData['md']);       

            /* Send event */
            $km->submit(); 
        } catch (Exception $e) {
            Log::info('KissmetricsTracker exception');
            Log::info($e->getMessage());
        }

        /* Return */
        return true;
    }
} /* KissmetricsTracker */
