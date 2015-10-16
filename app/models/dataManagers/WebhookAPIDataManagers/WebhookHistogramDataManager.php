<?php

class WebhookHistogramDataManager extends MultipleHistogramDataManager
{
    use WebhookDataManager;

    /**
     * getUrl
     * --------------------------------------------------
     * Returning the corresponding url.
     * @return string
     * --------------------------------------------------
    */
    protected function getUrl() {
        return $this->getCriteria()['url'];
    }

    public function getCurrentValue() {
        $data = $this->getJson();

        /* Checking the data. */
        if (is_null($data) || empty($data)) {
            return array();
        }

        return $data;
    }

    /**
     * testUrl
     * Testing if there's data on the provided url.
     * --------------------------------------------------
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function testUrl() {
        if ($this->getCurrentValue() == FALSE) {
            throw new ServiceException("There's no data on the provided url", 1);
        }
    }

}
?>
