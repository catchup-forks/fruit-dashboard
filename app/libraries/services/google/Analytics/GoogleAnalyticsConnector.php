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
     * disconnect
     * --------------------------------------------------
     * disconnecting the user from google-analytics.
     * @throws ServiceNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        parent::disconnect();
        /* deleting all plans. */
        GoogleAnalyticsProperty::where('user_id', $this->user->id)->delete();
    }
}