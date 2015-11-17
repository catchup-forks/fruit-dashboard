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
    private static $intercomIO;
    private static $customerIO;
    private static $mixpanel;
    private static $kissmetrics;

    /* -- Constructor -- */
    public function __construct(){
        self::$google   = new GoogleTracker();
        self::$intercomIO = new IntercomIOTracker();
        self::$customerIO = new CustomerIOTracker();
        self::$mixpanel = new MixpanelTracker();
        self::$kissmetrics = new KissmetricsTracker();
    }


    /**
     * ================================================== *
     *                PUBLIC STATIC SECTION               *
     * ================================================== *
     */
    /**
     * isTrackingEnabled:
     * --------------------------------------------------
     * Returns true if the tracking is enabled, false if it isn't.
     * @return (boolean) ($isTrackingEnabled) is the tracking enabled
     * --------------------------------------------------
     */
    public static function isTrackingEnabled() {
        /* ---- FORCE TRACKING (DEBUG MODE) ---- */
        //return true;

        /* Tracking is enabled only on production server */
        if (App::environment('production')) {
            return true;
        } else {
            return false;
        }
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
     *      (string) (ec) [Req] Event Category (GoogleAnalytics)
     *      (string) (ea) [Req] Event Action   (GoogleAnalytics)
     *      (string) (el) Event label.         (GoogleAnalytics)
     *      (int)    (ev) Event value.         (GoogleAnalytics)
     *      (string) (en) [Req] Event name     (IntercomIO)(CustomerIO)(Mixpanel)
     *      (array)  (md) Metadata             (IntercomIO)(CustomerIO)(Mixpanel)
     * @return None
     * --------------------------------------------------
     */
    public function trackAll($mode, $eventData) {
        if (self::isTrackingEnabled()) {
            /* Lazy mode */
            if ($mode=='lazy') {
                $googleEventData = array(
                    'ec' => $eventData['en'],
                    'ea' => $eventData['en'],
                    'el' => $eventData['el']
                );

                /* Intercom IO event data */
                $intercomIOEventData = array(
                    'en' => $eventData['en'],
                    'md' => array('metadata' => $eventData['el'])
                );

                /* Customer IO event data */
                $customerIOEventData = array(
                    'en' => $eventData['en'],
                    'md' => array('metadata' => $eventData['el'])
                );

                /* Mixpanel event data */
                $mixpanelEventData = array(
                    'en' => $eventData['en'],
                    'md' => array('metadata' => $eventData['el'])
                );

                /* Kissmetrics event data */
                $kissmetricsEventData = array(
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
                $intercomIOEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );

                /* Customer IO event data */
                $customerIOEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );

                /* Mixpanel event data */
                $mixpanelEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );

                /* Kissmetrics event data */
                $kissmetricsEventData = array(
                    'en' => $eventData['en'],
                    'md' => $eventData['md'],
                );
            }

            /* Send events */
            self::$google->sendEvent($googleEventData);
            self::$intercomIO->sendEvent($intercomIOEventData);
            self::$customerIO->sendEvent($customerIOEventData);
            self::$mixpanel->sendEvent($mixpanelEventData);
            self::$kissmetrics->sendEvent($kissmetricsEventData);

        }
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* GlobalTracker */