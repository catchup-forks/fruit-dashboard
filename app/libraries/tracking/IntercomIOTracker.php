<?php

/**
* --------------------------------------------------------------------------
* IntercomIOTracker:
*       Wrapper functions for server-side event tracking
* Usage:
*       $tracker = new IntercomIOTracker();
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
class IntercomIOTracker {

    /* -- Class properties -- */
    private static $intercomIO;

    /* -- Constructor -- */
    public function __construct(){
        self::$intercomIO = IntercomClient::factory(array(
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
            self::$intercomIO->createEvent(array(
                "event_name" => $eventData['en'],
                "created_at" => Carbon::now()->timestamp,
                "user_id" => (Auth::check() ? Auth::user()->id : 0),
                "metadata" => array_key_exists('md', $eventData) ? $eventData['md'] : null
            ));
        } catch (\Intercom\Exception\ClientErrorResponseException $e) {
        } catch (Exception $e) {
            Log::info('IntercomIOTracker exception');
            Log::info($e);
        }

        /* Return */
        return true;
    }
} /* IntercomIOTracker */