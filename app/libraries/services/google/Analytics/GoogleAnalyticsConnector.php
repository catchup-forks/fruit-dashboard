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
     * disconnecting the user from google-analytics.
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
     * populateData
     * Collecting the initial data from the service.
     */
    public function populateData() {
        Queue::push('GoogleAnalyticsPopulateData', array(
            'user_id'   => $this->user->id
        ));
    }

}