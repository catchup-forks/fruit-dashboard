<?php

/**
* -------------------------------------------------------------------------- 
* GlobalTracker: 
*       Wrapper functions for server-side event tracking    
* Usage:
*       $tracker = new GlobalTracker();
*       // Lazy mode
*       $tracker->trackAll('lazy', array(
*               'en' => 'Sign in', 
*               'el' => Auth::user()->email)
*           );
*       // Detailed mode
*       $tracker->trackAll('detailed', $eventData);
* -------------------------------------------------------------------------- 
*/
class GlobalTracker {
    /* -- Class properties -- */
    private static $google;
    private static $intercom;
    private static $mixpanel;
    
    /* -- Constructor -- */
    public function __construct(){
        self::$google   = new GoogleTracker();
        self::$intercom = new IntercomTracker();
        self::$mixpanel = new MixpanelTracker();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * trackAll: 
     * --------------------------------------------------
     * Tracks an event with all available tracking sites. In 'lazy mode'
     * eventData contains only the eventName and an eventOption string.
     * In 'detailed mode' the eventData contains all necessary options 
     * for all the tracking sites.
     * @param (string)  ($mode)    lazy | detailed
     * @param (array) ($eventData) The event data
     *    LAZY MODE
     *      (string) ($en) The name of the event
     *      (string) ($el) Custom label for the event
     *    DETAILED MODE
     *      (string) (ec) [Req] Event Category (Google)
     *      (string) (ea) [Req] Event Action   (Google)
     *      (string) (el) Event label.         (Google)
     *      (int)    (ev) Event value.         (Google)
     *      (string) (en) [Req] Event name     (Intercom)(Mixpanel)
     *      (array)  (md) Metadata             (Intercom)(Mixpanel)
     * @return None
     * --------------------------------------------------
     */
    public function trackAll($mode, $eventData) {
        if (App::environment('local')) {
            /* Lazy mode */
            if ($mode=='lazy') {
                $googleEventData = array(
                    'ec' => $eventData['en'],
                    'ea' => $eventData['en'],
                    'el' => $eventData['el']
                );

                /* Intercom IO event data */
                $intercomEventData = array(
                    'en' => $eventData['en'],
                    'md' => array('metadata' => $eventData['el'])
                );

                /* Mixpanel event data */
                $mixpanelEventData = array(
                    'en' => $eventData['en'],
                    'md' => array('metadata' => $eventData['el'])
                );

            /* Detailed option */
            } else {
                /* Google Analytics event data */
                $googleEventData = array(
                    'ec' => $eventData['ec'],
                    'ea' => $eventData['ea'],
                    'el' => $eventData['el'],
                    'ev' => $eventData['ev'],
                );

                /* Intercom IO event data */
                $intercomEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );

                /* Mixpanel event data */
                $mixpanelEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );
            }

            /* Send events */
            self::$google->sendEvent($googleEventData);
            self::$intercom->sendEvent($intercomEventData);
            self::$mixpanel->sendEvent($mixpanelEventData);
        }
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* GlobalTracker */