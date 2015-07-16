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
    /* Class properties */
    private $url;
    private $version;
    private $trackingID;
    private $clientID;

    /* Constructor */
    public function __construct(){
        $this->url         = 'https://www.google-analytics.com/collect';
        $this->version     = '1';
        $this->trackingID  = $_ENV['GOOGLE_TRACKING_CODE'];
        $this->clientID    = '444';
    }

    /**
     * sendEvent: 
     * --------------------------------------------------
     * Dispatches an event based on the arguments.
     * @param (dict) (eventData) The event data
     *     (string) (ec) [Req] Event Category.
     *     (string) (ea) [Req] Event Action.
     *     (string) (el) Event label.
     *     (int)    (ev) Event value.
     * @return (boolean) (status) True if production server, else false
     * --------------------------------------------------
     */
    public function sendEvent($eventData) {
        if (App::environment('production')) {
            /* Make the analytics url */
            $url = $this->makeEventUrl(
                $eventData['ec'], 
                $eventData['ea'],
                $eventData['el'],
                $eventData['ev']);

            /* Send the request */
            $client = new GuzzleClient();
            $response = $client->get($url);

            /* Return */
            return true;
        } else {
            /* Return */
            return false;
        }
    }

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
        /* Initialize replacer arrays to escape special chars in the url */
        $och = array(' ',   '/',  '\\' );
        $ech = array('%20', '%2', '%5C');
        
        /* Create url with data */
        $url = $this->url.'?';
        $url .= 'v='.$this->version;
        $url .= '&tid='.$this->trackingID;
        $url .= '&cid='.$this->clientID;
        $url .= '&t=event';
        $url .= '&ec='.str_replace($och, $ech, $ec);
        $url .= '&ea='.str_replace($och, $ech, $ea);
        if (!is_null($el)) {
            $url .= '&el='.str_replace($och, $ech, $el);
        };
        if (!is_null($ev)) {
            $url .= '&ev='.strval($ev);
        };

        /* Return */
        return $url;
    }
} /* GoogleTracker */