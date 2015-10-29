<?php

/* When a manager is using a webhook. */
trait WebhookDataManager
{
    /**
     * getJson
     * Returning the json from the url.
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

    /**
     * The descripor object.
     *
     * @var array
     */
    protected $descriptor = null;

    function __construct($data)
    {
        $this->dataObject = $data;
        $this->data = $data->decode();
        $this->criteria = $data->getCriteria();
        $this->user = $data->user();
        $this->descriptor = $data->getDescriptor();
    }

    abstract public function collect($options=array());

    /**
     * initialize
     * Default initializer
     */
    public function initialize()
    {
        $this->collect();
    }

    /**
     * build
     * Default data builder.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public function build()
    {
        return $this->data;
    }

    /**
     * isEmpty
     * Returning whether or not the data is empty.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public function isEmpty()
    {
        return $this->data == array();
    }

    /**
     * save
     * Saving the data to DB
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
     protected function save($data=null)
     {
        if ( ! is_null($data)) {
            $this->data = $data;
        }

        $this->dataObject->saveData($this->data);
     }
}
