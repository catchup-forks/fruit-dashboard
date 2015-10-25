<?php

/**
* -------------------------------------------------------------------------- 
* GoogleTracker: 
*       Wrapper functions for server-side event tracking    
* Usage:
*       $tracker = new GoogleTracker();
*       $eventData = array(
*           'ec' => 'Category', // Required.
*           'ea' => 'Action',   // Required.
*           'el' => 'Label',
*           'ev' => 0,
*       );
*       $tracker->sendEvent($eventData);
* -------------------------------------------------------------------------- 
*/
class GoogleTracker {
    /* -- Class properties -- */
    private static $url;
    private static $version;
    private static $trackingID;
    private static $clientID;

    /* -- Constructor -- */
    public function __construct(){
        self::$url         = 'https://www.google-analytics.com/collect';
        self::$version     = '1';
        self::$trackingID  = $_ENV['GOOGLE_TRACKING_CODE'];
        self::$clientID    = Session::getId();
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
     *     (string) (ec) [Req] Event Category.
     *     (string) (ea) [Req] Event Action.
     *     (string) (el) Event label.
     *     (int)    (ev) Event value.
     * @return (boolean) true
     * --------------------------------------------------
     */
    public function sendEvent($eventData) {
        /* Make the analytics url */
        $url = $this->makeEventUrl(
            $eventData['ec'], 
            $eventData['ea'],
            array_key_exists('el', $eventData) ? $eventData['el'] : null,
            array_key_exists('ev', $eventData) ? $eventData['ev'] : null
        );

        /* Send the request */
        try {
            $client = new GuzzleClient;
            $client->post($url['endpoint'], [
                'query' => $url['params']
            ]);
        } catch (Exception $e) {
            Log::info('GoogleTracker exception');
            Log::info($e->getMessage());
        }
        
        //$response = SiteFunctions::postUrl($url);

        /* Return */
        return true;
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * makeEventUrl: 
     * --------------------------------------------------
     * Makes the dispatch url from the arguments.
     * @param (string) (ec) [Req] Event Category.
     * @param (string) (ea) [Req] Event Action.
     * @param (string) (el) Event label.
     * @param (int)    (ev) Event value.
     * @return (string) (url) The event POST url
     * --------------------------------------------------
     */
    private function makeEventUrl($ec, $ea, $el, $ev) {       
        /* Create url with data */
        $url = array(
            'endpoint' => self::$url,
            'params'   => array(
                'v'     => self::$version,
                'tid'   => self::$trackingID,
                'cid'   => self::$clientID,
                't'     => 'event',
                'ec'    => $ec,
                'ea'    => $ea
            )
        );
       
        if (!is_null($el)) {
            $url['params']['el'] = $el;
        };
        if (!is_null($ev)) {
            $url['params']['ev'] = strval($ev);
        };

        /* Return */
        return $url;
    }
} /* GoogleTracker */
