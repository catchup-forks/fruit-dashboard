<?php

/**
* -------------------------------------------------------------------------- 
* GlobalTracker: 
*       Wrapper functions for server-side event tracking    
* Usage:
*       $tracker = new GlobalTracker();
*       // Easy option
*       $tracker->trackAll('Sign in', Auth::user());
*       // Detailed option
*       $tracker->trackAll($eventData);
* -------------------------------------------------------------------------- 
*/
class GlobalTracker {
    /* -- Class properties -- */
    private $google;
    private $intercom;
    private $mixpanel;
    
    /* -- Constructor -- */
    public function __construct(){
        $this->google    = new GoogleTracker();
        $this->intercom  = new IntercomTracker();
        $this->mixpanel  = new MixpanelTracker();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * trackAll: 
     * --------------------------------------------------
     * Tracks an event with all available tracking sites. If eventData 
     * is provided, the function makes the detailed tracking strings 
     * from the variable (detailed option). Otherwise it makes all 
     * strings from the eventName variable (easy option)
     * @param (string)  (eventName) The event name if we use the easy option 
     * @param (User)    ($user)     The event data 
     * @param (array)   (eventData) The event data if we use the detailed option
     *     (string) (ec) [Req] Event Category (Google)
     *     (string) (ea) [Req] Event Action   (Google)
     *     (string) (el) Event label.         (Google)
     *     (int)    (ev) Event value.         (Google)
     *     (string) (en) [Req] Event name     (Intercom)(Mixpanel)
     *     (array)  (md) Metadata             (Intercom)(Mixpanel)
     * @return None
     * --------------------------------------------------
     */
    public function trackAll($eventName, $user, $eventData=null) {
        /* Easy option */
        if ($eventData==null) {
            $googleEventData = array(
                'ec' => $eventName,
                'ea' => $eventName,
                'el' => $user->email,
                'ev' => $eventData['ev'],
            );

            /* Intercom IO event data */
            $intercomEventData = array(
                'en' => $eventName,
            );

            /* Mixpanel event data */
            $mixpanelEventData = array(
                'en' => $eventName,
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
        $this->google->sendEvent($googleEventData);
        $this->intercom->sendEvent($intercomEventData);
        //$this->mixpanel->sendEvent($mixpanelEventData);
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* GlobalTracker */