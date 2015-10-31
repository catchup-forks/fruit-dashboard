<?php

class BounceRateDataCollector extends HistogramDataCollector
{
    use GAHistogramDataCollectorTrait;

    protected static $metrics = array('bounceRate');

    public function getCurrentValue()
    {
        return $this->getCollector()->getBounceRate($this->getProfileId());
    }
}
?>
