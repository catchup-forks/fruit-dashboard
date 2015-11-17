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
        $this->user->googleAnalyticsProperties()->delete();
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
     * createDataObjects
     * Adding profile activation.
     * --------------------------------------------------
     * @param array $criteria
     * --------------------------------------------------
     */
    public function createDataObjects(array $criteria=array()) {
        /* Getting profile. */
        $profile = $this->user->googleAnalyticsProfiles()
            ->where('profile_id', $criteria['profile'])
            ->first(array('google_analytics_profiles.id'));
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
            $goal->active = true;
            $goal->save();

			/* Sending tracking event. */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Activation goal | Connected GA Goal',
                'el' => $this->user->email)
            );
        }
        /* Setting profile to active. */
        $profile->active = true;
        $profile->save();

        return parent::createDataObjects($criteria);
    }
}
