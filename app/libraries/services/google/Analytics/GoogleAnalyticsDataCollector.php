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
     * saveProperties
     * Saves a user's google analytics properties.
     */
    public function saveProperties() {
        $this->user->googleAnalyticsProperties()->delete();
        foreach ($this->getAccountIds() as $accountId) {
            $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($accountId);
            $items = $ga_properties->getItems();
            if (count($items) <= 0) {
                return null;
            }
            $properties = array();
            foreach ($items as $item) {
                $property = new GoogleAnalyticsProperty(array(
                    'id'         => $item->getId(),
                    'name'       => $item->getName(),
                    'account_id' => $accountId
                )); $property->user()->associate($this->user);
                $property->save();
                array_push($properties, $property);
            }
        }
        return $properties;
    }

    /**
     * getMetrics
     * Retrieving specific metrics for the selected property.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @param string $start
     * @param string $end
     * @param array $metrics
     * @param array $optParams
     * @return array
     * --------------------------------------------------
     */
    public function getMetrics($property, $start, $end, $metrics, $optParams=array()) {
        $useDimensions = array_key_exists('dimensions', $optParams);
        $metricsData = array();

        /* Iterating through the profiles. */
        foreach ($this->getProfiles($property) as $profile) {
            /* Retrieving results from API */
            $results = $this->analytics->data_ga->get(
               'ga:' . $profile->getId(), $start, $end, 'ga:' . implode(',ga:', $metrics), $optParams);
            $rows = $results->getRows();
            $profileName = $results->getProfileInfo()->getProfileName();

            Log::info($rows);

            if (count($rows) > 0) {
                /* Populating metricsData. */
                if ($useDimensions) {
                    $metricsData = $this->buildDimensionsData($metrics, $rows, $profileName);
                } else {
                    $metricsData = $this->buildSimpleMetricsData($metrics, $rows, $profileName);
                }
            } else {
            }
        }
        return $metricsData;
    }

    /**
    * buildSimpleMetricsData
    * Building dimension specific data.
     * --------------------------------------------------
    * @param array $metrics
    * @param array  $rows
    * @param string $profileName
    * @return array
     * --------------------------------------------------
    */
    private function buildSimpleMetricsData($metrics, $rows, $profileName) {
        /* Creating metrics array. */
        $metricsData = array();
        foreach ($metrics as $metric) {
            $metricsData[$metric] = array();
        }

        $i = 0;

        foreach ($metrics as $metric) {
            if ( ! array_key_exists($profileName, $metricsData[$metric])) {
                $metricsData[$metric][$profileName] = array();
            }
            array_push($metricsData[$metric][$profileName], $rows[0][$i++]);
        }
        return $metricsData;
    }

    /**
    * buildDimensionsData
    * Building dimension specific data.
     * --------------------------------------------------
    * @param array $metrics
    * @param array $rows
    * @param string $profileName
    * @return array
     * --------------------------------------------------
    */
    private function buildDimensionsData($metrics, $rows, $profileName) {
        /* Creating metrics array. */
        $metricsData = array();
        foreach ($metrics as $metric) {
            $metricsData[$metric] = array();
        }

        $i = 1;
        foreach ($metrics as $metric) {
            foreach ($rows as $row) {
                if ( ! array_key_exists($row[0], $metricsData[$metric])) {
                    $metricsData[$metric][$row[0]] = array();
                }
                array_push($metricsData[$metric][$row[0]], $row[$i]);
            }
            ++$i;
        }
        return $metricsData;
    }

    /**
     * getAvgSessionDuration
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    public function getAvgSessionDuration($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('avgSessionDuration'))['avgSessionDuration'];
   }


    /**
     * getSessions
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    public function getSessions($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('sessions'))['sessions'];
   }

    /**
     * getBounceRate
     * Returning the percentage of boucne rate.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    public function getBounceRate($property) {
        return $this->getMetrics($property, 'yesterday', 'today', array('bounceRate'))['bounceRate'];
   }

    /**
     * getProfiles
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    private function getProfiles($property) {
        return $this->analytics->management_profiles->listManagementProfiles($property->account_id, $property->id)->getItems();
   }
} /* GoogleAnalyticsDataCollector */
