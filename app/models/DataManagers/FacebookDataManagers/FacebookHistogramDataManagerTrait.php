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
            foreach ($this->getFirstValues()[0]['values'] as $dailyData) {
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

    /**
     * Getting the last DAYS entries for a specific insight
     *
     * @param string $insight
     * @return array
     */
    private function getFirstValues() {
        return $this->getCollector()->getInsight(
            static::$insight, $this->getPage()->id,
            array(
                'since' => Carbon::now()->subDays(SiteConstants::getFacebookPopulateDataDays())->getTimestamp(),
                'until' => Carbon::now()->getTimestamp(),
            )
        );
    }
}
?>
