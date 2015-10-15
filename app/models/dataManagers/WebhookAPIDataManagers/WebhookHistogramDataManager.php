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

        if (is_null($data) || empty($data)) {
            return array();
        }

        $decodedData = array();
        foreach ($data as $name=>$value) {
            if ( ! in_array($name, static::$staticFields) && is_numeric($value)) {
                $decodedData[$name] = $value;
            }
        }
        return $decodedData;
    }

}
?>
