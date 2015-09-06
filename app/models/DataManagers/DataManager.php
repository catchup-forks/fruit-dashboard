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
            $json = file_get_contents($this->getCriteria()['url']);
        } catch (Exception $e) {
            return null;
        }
        return json_decode($json, TRUE);
    }
}

/* This class is responsible for data collection. */
class DataManager extends Eloquent
{
    const ENTRIES = 15;

    /* -- Table specs -- */
    protected $table = "data_managers";

    /* -- Fields -- */
    protected $fillable = array(
        'data_id',
        'user_id',
        'descriptor_id',
        'settings_criteria'
    );
    public $timestamps = FALSE;

    public function collectData() {}

    /**
     * createManagerFromWidget
     * Creating and returning a manager from a widget
     * --------------------------------------------------
     * @param Widget $widget
     * @return array
     * --------------------------------------------------
     */
    public static function createManagerFromWidget($widget) {
        /* Only datawidgets are relevant */
        if ( ! $widget instanceof DataWidget) {
            return null;
        }

        /* Creating manager. */
        $generalManager = new DataManager;
        $generalManager->user()->associate($widget->user());
        $generalManager->descriptor()->associate($widget->descriptor);
        $generalManager->settings_criteria = json_encode($widget->getCriteria());

        /* Creating/assigning data. */
        if (isset($widget->data)) {
            $generalManager->data()->associate($widget->data);
        } else {
            $data = Data::create(array('raw_value' => ''));
           $generalManager->data()->associate($data);
        }

        /* Saving changes. */
        $generalManager->save();

        $manager = $generalManager->getSpecific();
        $manager->collectData();

        return $manager;

    }

    /* -- Relations -- */
    public function descriptor() { return $this->belongsTo('WidgetDescriptor'); }
    public function data() { return $this->belongsTo('Data', 'data_id'); }
    public function user() { return $this->belongsTo('User'); }
    public function widgets() {
        return $this->data->widgets();
    }

    public function getSpecific() {
        $className = str_replace('Widget', 'DataManager', WidgetDescriptor::find($this->descriptor_id)->getClassName());
        return $className::find($this->id);
    }

    /**
     * getCriteria
     * Returning the required settings for this widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getCriteria() {
        return json_decode($this->settings_criteria, 1);
    }

    /**
     * getData
     * Returning the raw data json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getData() {
        return json_decode($this->data->raw_value, 1);
    }

    /**
     * saveData
     * Saving the data to DB
     * --------------------------------------------------
     * @param data $data
     * --------------------------------------------------
     */
     public function saveData($data) {
        $this->data->raw_value = json_encode($data);
        $this->data->save();
     }

    /**
     * setWidgetsState
     * Setting the corresponding widgets state.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
     */
     public function setWidgetsState($state) {
        foreach ($this->widgets as $generalWidget) {
            $widget = $generalWidget->getSpecific();
            $widget->state = $state;
            $widget->save(array('skipManager' => TRUE));
        }
     }
}