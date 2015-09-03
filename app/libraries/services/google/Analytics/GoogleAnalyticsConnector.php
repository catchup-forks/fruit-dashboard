<?php

/**
* --------------------------------------------------------------------------
* GoogleAnalyticsConnector:
*   Connecting google's analytics service
* --------------------------------------------------------------------------
*/

class GoogleAnalyticsConnector extends GoogleConnector {
    protected static $service = 'google_analytics';
    protected static $scope = Google_Service_Analytics::ANALYTICS_READONLY;

    /**
     * The analytics object.
     *
     * @var Google_Service_Analytics
     */
    private $analytics = null;

    /**
     * Overloading getTokens to save properties.
     */
    public function getTokens($code) {
        parent::getTokens($code);
        /* Getting facebook pages  (will be moved to autodashboard) */
        $collector = new GoogleAnalyticsDataCollector(Auth::user());
        $collector->saveProperties();
    }

    /**
     * Overloading connect to create the analytics object.
     */
    public function connect() {
        parent::connect();
        $this->analytics = new Google_Service_Analytics($this->client);
    }
}