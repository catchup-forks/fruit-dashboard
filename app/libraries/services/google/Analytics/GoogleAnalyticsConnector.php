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
     * disconnect
     * disconnecting the user from google analytics.
     * --------------------------------------------------
     * @throws ServiceNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        parent::disconnect();
        /* deleting all plans. */
        GoogleAnalyticsProperty::where('user_id', $this->user->id)->delete();
    }

    /**
     * saveTokens
     * Retrieving the access, and refresh tokens from authentication code.
     * --------------------------------------------------
     * @param array $parameters
     * @return None
     * @throws GoogleConnectFailed
     * --------------------------------------------------
     */
    public function saveTokens(array $parameters=array()) {
        parent::saveTokens($parameters);
        $collector = new GoogleAnalyticsDataCollector($this->user, $this);
        $collector->saveProperties();
    }

}