<?php

/* When a manager is using a webhook. */
trait WebhookDataManager {
    /**
     * getJson
     * Returning the json from the url.
     * --------------------------------------------------
     * @return array/null
     * --------------------------------------------------
     */
    private function getJson() {
        try {
            $json = file_get_contents($this->criteria['url']);
        } catch (Exception $e) {
            return null;
        }
        return json_decode($json, TRUE);
    }
}

/* This class is responsible for data collection. */
abstract class DataManager
{
    /**
     * The Data object.
     *
     * @var Data
     */
    private $dataObject = null;

    /**
     * The User object.
     *
     * @var User
     */
    protected $user = null;

    /**
     * The decoded data.
     *
     * @var array
     */
    protected $data = null;

    /**
     * The Criteria object.
     *
     * @var array
     */
    protected $criteria = null;

    function __construct($data) {
        $this->dataObject = $data;
        $this->data = $data->decode();
        $this->criteria = $data->getCriteria();
        $this->user = $data->user();
    }

    abstract public function collect($options=array());
    abstract public function initialize();

    /**
     * save
     * Saving the data to DB
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
     public function save($data=null) {
        if ( ! is_null($data)) {
            $this->data = $data;
        }
        $this->dataObject->saveData($this->data);
     }
}