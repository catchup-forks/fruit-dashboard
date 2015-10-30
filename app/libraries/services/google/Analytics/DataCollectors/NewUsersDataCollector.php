<?php

class NewUsersDataCollector extends MultipleHistogramDataCollector
{
    use GAHistogramBySourceDataCollectorTrait;

    protected static $metrics = array('newUsers');

    public function getCurrentValue() {
        return $this->getCollector()->getNewUsers($this->getProfileId());
    }
}
?>
