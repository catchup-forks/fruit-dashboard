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
        return $this->criteria['url'];
    }

    public function getCurrentValue() {
        $data = $this->getJson();

        /* Checking the data. */
        if (is_null($data) || empty($data)) {
            throw new ServiceException;
        }

        return $data;
    }

}
?>
