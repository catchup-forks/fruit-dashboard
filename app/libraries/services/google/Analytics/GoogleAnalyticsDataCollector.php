<?php

/**
* --------------------------------------------------------------------------
* GoogleAnalyticsDataCollector:
*       Getting data from google account.
* --------------------------------------------------------------------------
*/

class GoogleAnalyticsDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $client;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new GoogleConnector($user);
        $connector->connect();
        $this->client = $connector->getClient();
    }


} /* GoogleAnalyticsDataCollector */
