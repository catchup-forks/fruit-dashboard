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
     * getSessions
     * Returning the number of sessions.
     */
    public function getSessions($propertyId) {
        foreach ($this->getProfiles($propertyId) as $profile) {
            $results = $this->analytics->data_ga->get(
               'ga:' . $profile->getId(),
               'yesterday',
               'today',
               'ga:avgSessionDuration');

            if (count($results->getRows()) > 0) {
                // Get the profile name.
                $profileName = $results->getProfileInfo()->getProfileName();
                Log::info($profileName);

                // Get the entry for the first entry in the first row.
                $rows = $results->getRows();
                Log::info($rows);
                $sessions = $rows[0][0];
            }
        }
        return null;
   }

    /**
     * getProfiles
     */
    private function getProfiles($propertyId) {
        return $this->analytics->management_profiles->listManagementProfiles($this->getFirstAccountId(), $propertyId)->getItems();
   }
} /* GoogleAnalyticsDataCollector */
