<?php

class SessionsDataCollector extends MultipleHistogramDataCollector
{
    use GAHistogramBySourceDataCollectorTrait;

    protected static $metrics = array('sessions');

    public function getCurrentValue() {
        return $this->getCollector()->getSessions($this->getProfileId());
    }
}
?>
