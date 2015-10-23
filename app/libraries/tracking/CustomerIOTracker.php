<?php

/**
* --------------------------------------------------------------------------
* CustomerIOTracker:
*       Wrapper functions for server-side event tracking
* Usage:
*       $tracker = new CustomerIOTracker();
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
class CustomerIOTracker {

    /* -- Class properties -- */
    private static $site_id;
    private static $api_key;

    /* -- Constructor -- */
    public function __construct(){
        self::$site_id      = $_ENV['CUSTOMER_IO_SITE_ID'];
        self::$api_key      = $_ENV['CUSTOMER_IO_API_KEY'];
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
        /* Build data */
        $data = array(
            'name' => $eventData['en'],
            'data' => $eventData['md'],
        );

        try {
            $url = 'https://track.customer.io/api/v1/customers/' . strval(Auth::user()->id) . '/events';

            /* Build session and send the request */
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $url);
            curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($session, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($session, CURLOPT_USERPWD, self::$site_id . ':' . self::$api_key);
            curl_exec($session);
            curl_close($session);
        } catch (Exception $e) {
            Log::info('CustomerIOTracker exception');
            Log::info($e);
        }

        /* Return */
        return true;
    }
} /* CustomerIOTracker */