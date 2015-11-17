<?php

class WebhookDataCollector extends MultipleHistogramDataCollector
{
    /**
     * Return the json from the url.
     * --------------------------------------------------
     * @return array/null
     * --------------------------------------------------
     */
    private function getJson()
    {
        try {
            $json = file_get_contents($this->criteria['url']);
        } catch (Exception $e) {
            return null;
        }
        return json_decode($json, true);
    }

    /**
     * getUrl
     * --------------------------------------------------
     * Return the corresponding url.
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
            throw new ServiceException('Invalid JSON returned by URL: '. $this->getUrl());
        }

        return $data;
    }

}
?>
