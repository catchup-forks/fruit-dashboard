<?php

trait FBHistogramDataCollectorTrait
{
    use FBDataCollectorTrait;

    /**
     * getCurrentValue
     * Return the current value.
     */
    public function getCurrentValue()
    {
        /* Getting the page from settings. */
        return $this->getCollector()->getInsightCurrentValue(
            $this->getPageId(),
            static::$insight,
            static::$period
        );
    }

    /**
     * initialize
     * Creating, and saving data.
     */
    public function initialize()
    {
        try {
            $data = array();
            foreach ($this->getCollector()->getPopulateHistogram(
                $this->getPageId(),
                static::$insight)[0]['values'] as $dailyData) {
                $date = Carbon::createFromTimestamp(
                    strtotime($dailyData['end_time'])
                );
                array_push($data, array(
                    'value'     => $dailyData['value'],
                    'timestamp' => $date->getTimestamp()
                ));
            }
            foreach ($data as $entry) {
                $this->collect(array(
                    'entry' => $entry,
                    'sum'   => $this->isCumulative()
                ));
            }
        } catch (ServiceException $e) {
            Log::error('Facebook connection error. ' . $e->getMessage());
            $this->delete();
        }
    }

    /**
     * getCollector
     * Return a data collector
     * --------------------------------------------------
     * @return FacebookDataCollector
     * --------------------------------------------------
     */
    private function getCollector()
    {
        $collector = new FacebookDataCollector($this->user);
        return $collector;
    }

}
?>
