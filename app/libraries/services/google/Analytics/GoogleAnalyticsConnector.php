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
    public function getTokens(array $parameters=array()) {
        parent::getTokens($parameters);
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

    /**
     * createDataManagers
     * --------------------------------------------------
     * Creating the data managers for each page.
     * --------------------------------------------------
     */
    protected function createDataManagers() {
        $dataManagers = array();
        foreach ($this->user->googleAnalyticsProperties()->get() as $property) {
            foreach (parent::createDataManagers() as $dataManager) {
                $dataManager->settings_criteria = json_encode(array(
                    'property' => $property->id
                ));
                array_push($dataManager->save());
            }
        }
        return $dataManagers;
    }
}