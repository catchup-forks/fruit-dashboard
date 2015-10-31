<?php

class AvgSessionDurationDataCollector extends HistogramDataCollector
{
    use GAHistogramDataCollectorTrait;

    protected static $metrics = array('avgSessionDuration');

    public function getCurrentValue()
    {
        return $this->getCollector()->getAvgSessionDuration($this->getProfileId());
    }
}
?>
