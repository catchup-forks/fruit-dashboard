<?php

trait GoogleAnalyticsDataManagerTrait
{
    /**
     * getProperty
     * --------------------------------------------------
     * Returning the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getProperty() {
        $profileId = $this->getCriteria()['profile'];
        $profile = $this->user->googleAnalyticsProfiles()->where('profile_id', $profileId)->first();
        /* Invalid profile in DB. */
        if (is_null($profile)) {
            return null;
        }
        $property = $this->user->googleAnalyticsProperties()->where('property_id', $profile->property_id)->first();
        return $property;
    }

    /**
     * flatData
     * --------------------------------------------------
     * Returning a flattened data.
     * @param $insightData
     * --------------------------------------------------
    */
    protected function flatData($insightData) {
        return $insightData;

        $newData = array();
        foreach ($insightData as $name=>$dataAsArray) {
            $newData[$name] = $dataAsArray[0];
        }
        return $newData;
    }
}
?>
