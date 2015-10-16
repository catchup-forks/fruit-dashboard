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

    /**
     * createDataManagers
     * Adding profile activation.
     * --------------------------------------------------
     * @param array $criteria
     * --------------------------------------------------
     */
    public function createDataManagers(array $criteria=array()) {
        /* Getting profile. */
        $profile = $this->user->googleAnalyticsProfiles()
            ->where('profile_id', $criteria['profile'])->first();
        if (is_null($profile)) {
            throw new ServiceException("Selected profile not found.", 1);
        }

        if (array_key_exists('goal', $criteria)) {
            $goal = $profile->goals()
                ->where('goal_id', $criteria['goal'])
                ->first();
            if (is_null($goal)) {
                throw new ServiceException("Selected goal not found.", 1);
            }
            $goal->active = TRUE;
            $goal->save();
        }
        /* Setting profile to active. */
        $profile->active = TRUE;
        $profile->save();

        return parent::createDataManagers($criteria);
    }

}