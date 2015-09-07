<?php

class WebhookHistogramDataManager extends HistogramDataManager
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
        if (isset($data['value'])) {
            return $data['value'];
        }
        return 0;
    }

}
?>
