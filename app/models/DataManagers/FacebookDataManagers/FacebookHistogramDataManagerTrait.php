<?php

trait FacebookHistogramDataManagerTrait
{
    use FacebookDataManagerTrait;

    /**
     * getCurrentValue
     * Returning the current value.
     */
    public function getCurrentValue() {
        /* Getting the page from settings. */
        return $this->getCollector()->getInsightCurrentValue($this->getPage()->id, static::$insight, static::$period);
    }

    /**
     * initializeData
     * Creating, and saving data.
     */
    public function initializeData() {
        try {
            $data = array();
            foreach ($this->getCollector()->getPopulateHistogram($this->getPage()->id, static::$insight)[0]['values'] as $dailyData) {
                $date = Carbon::createFromTimestamp(strtotime($dailyData['end_time']));
                array_push($data, array(
                    'value'     => $dailyData['value'],
                    'timestamp' => $date->getTimestamp()
                ));
            }
            $this->saveData($data);
        } catch (ServiceException $e) {
            Log::error('Facebook connection error. ' . $e->getMessage());
            $this->delete();
        }
    }

    /**
     * getCollector
     * Returning a data collector
     * --------------------------------------------------
     * @return FacebookDataCollector
     * --------------------------------------------------
     */
    private function getCollector() {
        $collector = new FacebookDataCollector($this->user);
        return $collector;
    }

}
?>
