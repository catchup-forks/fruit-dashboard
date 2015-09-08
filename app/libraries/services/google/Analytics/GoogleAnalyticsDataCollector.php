<?php

/**
* --------------------------------------------------------------------------
* GoogleAnalyticsDataCollector:
*       Getting data from google account.
* --------------------------------------------------------------------------
*/

class GoogleAnalyticsDataCollector
{
    /**
     * The user object.
     *
     * @var User
     */
    private $user;

    /**
     * The client object.
     *
     * @var Google_Client
     */
    private $client;

    /**
     * The analytics object.
     *
     * @var Google_Service_Analytics
     */
    private $analytics;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new GoogleAnalyticsConnector($user);
        $connector->connect();
        $this->client = $connector->getClient();
        $this->analytics = new Google_Service_Analytics($this->client);
    }

    /**
     * getFirstAccountId
     * Returning the first account id.
     */
    private function getFirstAccountId() {
        /* Getting accounts */
        $accounts = $this->analytics->management_accounts->listManagementAccounts();
        $items = $accounts->getItems();
        if (count($items) <= 0) {
            return null;
        }

        /* Getting properties */
        return $items[0]->getId();
    }

    /**
     * saveProperties
     * Saves a user's google analytics properties.
     */
    public function saveProperties() {
        $this->user->googleAnalyticsProperties()->delete();
        $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($this->getFirstAccountId());
        $items = $ga_properties->getItems();
        if (count($items) <= 0) {
            return null;
        }
        $properties = array();
        foreach ($items as $item) {
            $property = new GoogleAnalyticsProperty(array(
                'id'   => $item->getId(),
                'name' => $item->getName()
            ));
            $property->user()->associate($this->user);
            $property->save();
            array_push($properties, $property);
        }
        return $properties;
    }

    /**
     * getMetrics
     * Retrieving specific metrics for all profiles.
     */
    public function getMetrics($propertyId, $start, $end, $metrics) {
        /* Creating metrics array. */
        $metricsData = array();
        foreach ($metrics as $metric) {
            $metricsData[$metric] = array();
        }

        /* Iterating through the profiles. */
        foreach ($this->getProfiles($propertyId) as $profile) {
            /* Retrieving results from API */
            $results = $this->analytics->data_ga->get(
               'ga:' . $profile->getId(), $start, $end, 'ga:' . implode(',ga:', $metrics));
            $rows = $results->getRows();

            if (count($rows) > 0) {
                $profileName = $results->getProfileInfo()->getProfileName();

                /* Populating metricsData. */
                $i = 0;
                foreach ($metrics as $metric) {
                    if (!isset($metricsData[$metric][$profileName])) {
                        $metricsData[$metric][$profileName] = array();
                    }
                    array_push($metricsData[$metric][$profileName], $rows[0][$i++]);
                }
            }
        }
        return $metricsData;
    }

    /**
     * getAvgSessionDuration
     * Returning the number of sessions.
     */
    public function getAvgSessionDuration($propertyId) {
        return $this->getMetrics($propertyId, 'yesterday', 'today', array('avgSessionDuration'))['avgSessionDuration'];
   }


    /**
     * getSessions
     * Returning the number of sessions.
     */
    public function getSessions($propertyId) {
        return $this->getMetrics($propertyId, 'yesterday', 'today', array('sessions'))['sessions'];
   }

    /**
     * getBounceRate
     * Returning the percentage of boucne rate.
     */
    public function getBounceRate($propertyId) {
        return $this->getMetrics($propertyId, 'yesterday', 'today', array('bounceRate'))['bounceRate'];
   }

    /**
     * getProfiles
     */
    private function getProfiles($propertyId) {
        return $this->analytics->management_profiles->listManagementProfiles($this->getFirstAccountId(), $propertyId)->getItems();
   }
} /* GoogleAnalyticsDataCollector */
