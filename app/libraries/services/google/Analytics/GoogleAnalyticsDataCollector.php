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
    function __construct($user, $connector=null) {
        $this->user = $user;
        if (is_null($connector)) {
            $connector = new GoogleAnalyticsConnector($user);
            $connector->connect();
        }
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
        $properties = array();
        foreach ($this->getAccountIds() as $accountId) {
            $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($accountId);
            $items = $ga_properties->getItems();
            if (count($items) <= 0) {
                continue;
            }
            foreach ($items as $item) {
                $property = new GoogleAnalyticsProperty(array(
                    'id'         => $item->getId(),
                    'name'       => $item->getName(),
                    'account_id' => $accountId
                ));
                $property->user()->associate($this->user);
                array_push($properties, $property);
            }
        }

        if (count($properties) > 0) {
            /* Only refreshing if we have results. */
            $this->user->googleAnalyticsProperties()->delete();
            foreach ($properties as $property) {
                $property->save();
            }
        }
        return $properties;
    }

    /**
     * getMetrics
     * Retrieving specific metrics for the selected property.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @param int $profileId
     * @param string $start
     * @param string $end
     * @param array $metrics
     * @param array $optParams
     * @return array
     * --------------------------------------------------
     */
    public function getMetrics($property, $profileId, $start, $end, $metrics, $optParams=array()) {
        $useDimensions = array_key_exists('dimensions', $optParams);
        $metricsData = array();

        /* Iterating through the profiles. */
        foreach ($this->getProfiles($property) as $profile) {
            if ($profile->id != $profileId) {
                continue;
            }
            /* Retrieving results from API */
            try {
                $results = $this->analytics->data_ga->get('ga:' . $profile->getId(), $start, $end, 'ga:' . implode(',ga:', $metrics), $optParams);
            } catch (Exception $e) {
                Log::error($e->getMessage());
                throw new ServiceException("Google connection error.", 1);
            }

            $rows = $results->getRows();
            $profileName = $results->getProfileInfo()->getProfileName();

            if (count($rows) > 0) {
                /* Populating metricsData. */
                if ($useDimensions) {
                    $metricsData = $this->buildDimensionsData($metrics, $rows, $profileName);
                } else {
                    $metricsData = $this->buildSimpleMetricsData($metrics, $rows);
                }
            } else if( ! $useDimensions) {
                $rows = array();
                foreach ($metrics as $metric) {
                    array_push($rows, 0);
                }
                $metricsData = $this->buildSimpleMetricsData($metrics, array($rows), $profileName);
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
    * @return array
     * --------------------------------------------------
    */
    private function buildSimpleMetricsData($metrics, $rows) {
        /* Creating metrics array. */
        $metricsData = array();
        $i = 0;
        foreach ($metrics as $metric) {
            $metricsData[$metric] = $rows[0][$i++];
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
                $metricsData[$metric][$row[0]] = $row[$i];
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
     * @param $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getAvgSessionDuration($property, $profileId) {
        return $this->getMetrics($property, $profileId, SiteConstants::getGoogleAnalyticsLaunchDate(), 'today', array('avgSessionDuration'))['avgSessionDuration'];
   }


    /**
     * getSessions
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @param $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getSessions($property, $profileId) {
        return $this->getMetrics($property, $profileId, SiteConstants::getGoogleAnalyticsLaunchDate(), 'today', array('sessions'))['sessions'];
   }

    /**
     * getBounceRate
     * Returning the percentage of bounce rate.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @param $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getBounceRate($property, $profileId) {
        return $this->getMetrics($property, $profileId, SiteConstants::getGoogleAnalyticsLaunchDate(), 'today', array('bounceRate'))['bounceRate'];
   }

    /**
     * getProfiles
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    public function getProfiles($property) {
        try {
            return $this->analytics->management_profiles->listManagementProfiles($property->account_id, $property->id)->getItems();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
   }
} /* GoogleAnalyticsDataCollector */
