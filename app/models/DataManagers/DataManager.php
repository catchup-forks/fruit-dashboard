<?php

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

    /* -- Relations -- */
    public function descriptor() { return $this->belongsTo('WidgetDescriptor'); }
    public function data() { return $this->belongsTo('Data', 'data_id'); }
    public function user() { return $this->belongsTo('User'); }
    public function widgets() { return $this->hasManyThrough('Widget', 'Data'); }

    public function getSpecific() {
        $className = str_replace('Widget', 'DataManager', WidgetDescriptor::find($this->descriptor_id)->getClassName());
        return $className::find($this->id);
    }

    /**
     * collectData
     * Getting the new value based on getCurrentValue()
     */
    public function collectData() {
        $newValue = $this->getCurrentValue();

        /* Getting previous values. */
        $currentData = json_decode($this->data->raw_value, 1);
        $lastData = end($currentData);
        $today = Carbon::now()->toDateString();
        /* If today, popping the old value. */
        if ($lastData && ($lastData['date'] == $today)) {
            array_pop($currentData);
        }
        /* Adding, saving data. */
        array_push($currentData, array('date' => $today, 'value' => $newValue));
        $this->data->raw_value = json_encode($currentData);
        $this->data->save();
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
     protected function saveData($data) {
        $this->data->raw_value = json_encode($data);
        $this->data->save();
     }

}