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
     * saveProperties
     * Saves a user's google analytics properties.
     */
    public function saveProperties() {
        $properties = array();
        foreach ($this->getAccountIds() as $accountId) {
            /* Gathering data from google */
            $ga_properties = $this->analytics->management_webproperties->listManagementWebproperties($accountId);
            $items = $ga_properties->getItems();
            if (count($items) <= 0) {
                continue;
            }
            foreach ($items as $item) {
                /* Saving properties */
                $propertyId = $item->getId();
                $property = new GoogleAnalyticsProperty(array(
                    'name'        => $item->getName(),
                    'account_id'  => $accountId,
                    'property_id' => $propertyId
                ));
                $property->user()->associate($this->user);
                array_push($properties, $property);
            }
        }
        if (count($properties) > 0) {
            /* Only refreshing if we have results. */
            $this->user->googleAnalyticsProperties()->delete();
            $this->user->googleAnalyticsGoals()->delete();
            $this->user->googleAnalyticsProfiles()->delete();
            foreach ($properties as $property) {
                $property->save();
                /* Saving profiles */
                $this->saveProfiles($property);
                $this->saveGoals($property);
            }

        }
        return $properties;
    }

    /**
     * saveGoals
     * Saves a user's google analytics goals.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * --------------------------------------------------
     */
    private function saveGoals($property) {
        /* Gathering data from google */
        $analyticsGoals = $this->getGoals($property);
        foreach ($analyticsGoals as $iGoal) {
            $profile = $this->user->googleAnalyticsProfiles()->where('profile_id', $iGoal->getProfileId())->first();
            if (is_null($profile)) {
                continue;
            }
            /* Saving properties */
            $goal = new GoogleAnalyticsGoal(array(
                'name'        => $iGoal->getName(),
                'goal_id'     => $iGoal->getId(),
            ));
            $goal->profile()->associate($profile);
            $goal->save();
        }
    }

    /**
     * saveProfiles
     * Saves a user's google analytics profiles.
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * --------------------------------------------------
     */
    private function saveProfiles($property) {
        /* Gathering data from google */
        $analyticsProfiles = $this->getProfiles($property);
        foreach ($analyticsProfiles as $iProfile) {
            /* Saving properties */
            $profile = new GoogleAnalyticsProfile(array(
                'name'       => $iProfile->getName(),
                'profile_id' => $iProfile->getId(),
            ));
            $profile->property()->associate($property);
            $profile->save();
        }
    }

    /**
     * getMetrics
     * Retrieving specific metrics for the selected property.
     * --------------------------------------------------
     * @param GoogleAnalyticsProfile $profileId
     * @param string $start
     * @param string $end
     * @param array $metrics
     * @param array $optParams
     * @return array
     * --------------------------------------------------
     */
    public function getMetrics($profileId, $start, $end, $metrics, $optParams=array()) {
        $useDimensions = array_key_exists('dimensions', $optParams);
        $metricsData = array();

        try {
            /* Retrieving results from API */
            $results = $this->analytics->data_ga->get('ga:' . $profileId, $start, $end, 'ga:' . implode(',ga:', $metrics), $optParams);
        } catch (ServiceException $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
        /* Getting rows. */
        $rows = $results->getRows();

        if (count($rows) > 0) {
            /* Populating metricsData. */
            if ($useDimensions) {
                $metricsData = $this->buildDimensionsData($metrics, $rows);
            } else {
                $metricsData = $this->buildSimpleMetricsData($metrics, $rows);
            }
        } else if( ! $useDimensions) {
            /* The value is 0, for all the data. */
            $rows = array();
            foreach ($metrics as $metric) {
                array_push($rows, 0);
            }
            $metricsData = $this->buildSimpleMetricsData($metrics, array($rows));
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
    * @return array
     * --------------------------------------------------
    */
    private function buildDimensionsData($metrics, $rows) {
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
     * @param string $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getAvgSessionDuration($profileId) {
        return $this->getMetrics(
            $profileId,
            'yesterday', 'today', array('avgSessionDuration')
        )['avgSessionDuration'];
   }

    /**
     * getSessionsPerUser
     * Returning the number of sessions per user.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getSessionsPerUser($profileId) {
        return $this->getMetrics(
            $profileId,
            'yesterday', 'today', array('sessionsPerUser')
        )['sessionsPerUser'];
   }

    /**
     * getSessions
     * Returning the number of sessions.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getSessions($profileId) {
        return $this->getMetrics(
            $profileId,
            'yesterday', 'today', array('sessions')
        )['sessions'];
   }

    /**
     * getUsers
     * Returning the number of users.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getUsers($profileId) {
        return $this->getMetrics(
            $profileId,
            'yesterday', 'today', array('users')
        )['users'];
   }

    /**
     * getBounceRate
     * Returning the percentage of bounce rate.
     * --------------------------------------------------
     * @param string $profileId
     * @return array
     * --------------------------------------------------
     */
    public function getBounceRate($profileId) {
        return $this->getMetrics(
            $profileId,
            'yesterday', 'today', array('bounceRate')
        )['bounceRate'];
   }

    /**
     * getProfiles
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    private function getProfiles($property) {
        try {
            return $this->analytics->management_profiles->listManagementProfiles($property->account_id, $property->property_id)->getItems();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
    }

    /**
     * getGoals
     * --------------------------------------------------
     * @param GoogleAnalyticsProperty $property
     * @return array
     * --------------------------------------------------
     */
    private function getGoals($property) {
        try {
            return $this->analytics->management_goals->listManagementGoals($property->account_id, $property->property_id, '~all')->getItems();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Google connection error.", 1);
        }
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

} /* GoogleAnalyticsDataCollector */
