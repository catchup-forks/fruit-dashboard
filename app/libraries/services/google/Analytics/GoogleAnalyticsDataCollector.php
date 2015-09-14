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
     * getAccountIds
     * Returning the first account id.
     */
    private function getAccountIds() {
        /* Getting accounts */
        $accounts = $this->analytics->management_accounts->listManagementAccounts();
        $items = $accounts->getItems();
        if (count($items) <= 0) {
            return null;
        }

        /* Getting properties */
        $ids = array();
        foreach ($items as $account) {
            array_push($ids, $account->getId());
        }
        return $ids;
    }

    /**
     * getProperties
     * Saves a user's google analytics properties.
     */
    public function getProperties() {
        $this->user->googleAnalyticsProperties()->delete();
        foreach ($this->getAccountIds() as $accountId) {
            $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($accountId);
            $items = $ga_properties->getItems();
            if (count($items) <= 0) {
                return null;
            }
            $properties = array();
            foreach ($items as $item) {
                $properties[$accountId . ',' . $item->getId()] = $item->getName();
            }
        }
        return $properties;
    }

    /**
     * getMetrics
     * Retrieving specific metrics for all profiles.
     */
    public function getMetrics($property, $start, $end, $metrics) {
        /* Creating metrics array. */
        $metricsData = array();
        foreach ($metrics as $metric) {
            $metricsData[$metric] = array();
        }

        /* Iterating through the profiles. */
        foreach ($this->getProfiles($property) as $profile) {
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
        } return $metricsData;
    }

    /**
     * getAvgSessionDuration
     * Returning the number of sessions.
     */
    public function getAvgSessionDuration($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('avgSessionDuration'))['avgSessionDuration'];
   }


    /**
     * getSessions
     * Returning the number of sessions.
     */
    public function getSessions($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('sessions'))['sessions'];
   }

    /**
     * getBounceRate
     * Returning the percentage of boucne rate.
     */
    public function getBounceRate($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('bounceRate'))['bounceRate'];
   }

    /**
     * getProfiles
     */
    private function getProfiles($property) {
        return $this->analytics->management_profiles->listManagementProfiles($property->account_id, $property->id)->getItems();
   }
} /* GoogleAnalyticsDataCollector */
