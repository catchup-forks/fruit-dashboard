<?php

class UsersDataCollector extends MultipleHistogramDataCollector
{
    use GAHistogramBySourceDataCollectorTrait;

    protected static $metrics = array('users');

    public function getCurrentValue()
    {
        return $this->getCollector()->getUsers($this->getProfileId());
    }
}
?>
