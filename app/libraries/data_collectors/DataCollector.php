<?php

/* This class is responsible for data collection. */
abstract class DataCollector
{
    /**
     * The id of the data.
     *
     * @var int
     */
    private $data_id = null;

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
     * The Descriptor attributes.
     *
     * @var array
     */
    protected $descriptorAttributes = null;

    public function __construct($data)
    {
        $this->data_id = $data->id;
        $this->data = $data->decode();
        $this->criteria = $data->getCriteria();
        $this->user = $data->user();
        $this->descriptorAttributes = $data->getDescriptor()->getAttributes();
    }

    abstract public function collect($options = array());

    /**
     * initialize
     * Default initializer
     */
    public function initialize()
    {
        $this->collect();
    }

    /**
     * save
     * Saving the data to DB
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
     protected function save($data = null)
     {
        if ( ! is_null($data)) {
            $this->data = $data;
        }

        $dataObject = Data::find($this->data_id);

        $dataObject->saveData($this->data);
     }
}
