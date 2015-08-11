<?php

/**
* --------------------------------------------------------------------------
* IntercomTracker:
*       Wrapper functions for server-side event tracking
* Usage:
*       $tracker = new IntercomTracker();
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
class IntercomTracker {

    /* -- Class properties -- */
    private static $intercom;

    /* -- Constructor -- */
    public function __construct(){
        self::$intercom = IntercomClient::factory(array(
            'app_id'  => $_ENV['INTERCOM_APP_ID'],
            'api_key' => $_ENV['INTERCOM_API_KEY'],
        ));
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
        /* Build and send the request */
        try {
            self::$intercom->createEvent(array(
                "event_name" => $eventData['en'],
                "created_at" => Carbon::now()->timestamp,
                "user_id" => (Auth::check() ? Auth::user()->id : 0),
                "metadata" => array_key_exists('md', $eventData) ? $eventData['md'] : null
            ));
        } catch (Intercom\Exception\ClientErrorResponseException $e) {
            if (Auth::check()) {
                $self::$intercom->updateUser(array(
                    'user_id'         => Auth::user()->id,
                    'last_request_at' => Carbon::now()->timestamp,
                    "metadata"        => array_key_exists('md', $eventData) ? $eventData['md'] : null
                ));
            }
        }

        /* Return */
        return true;
    }
} /* IntercomTracker */