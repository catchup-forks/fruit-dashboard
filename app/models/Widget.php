<?php

class Widget extends Eloquent
{

    // -- Table specs -- //
    protected $table = "widgets";

    // -- Fields -- //
    protected $fillable = array(
        'state',
        'settings',
        'position'
    );
    public $timestamps = FALSE;

    // These variable will be overwritten, with late static binding.
    public static $type = null;
    public static $settingsFields = array();
    public static $setupSettings = array();

    // -- Relations -- //
    public function descriptor() { return $this->belongsTo('WidgetDescriptor'); }
    public function data() { return $this->hasOne('Data'); }
    public function dashboard() { return $this->belongsTo('Dashboard'); }
    public function user() { return $this->dashboard->user; }

    /**
     * Getting the type of the widget.
     *
     * @returns string widget Type
    */
    public function getType() {
        return static::$type;
    }
    /**
     * Getting the settings meta.
     *
     * @returns array The widget settings meta.
    */
    public function getSettingsFields() {
        return static::$settingsFields;
    }

    /**
     * Getting the setup settings meta.
     *
     * @returns array The widget settings meta.
    */
    public function getSetupFields() {
        return static::$setupSettings;
    }

     /**
     * Getting the correct widget from a general widget.
     *
     * @returns mixed A specific Widget object.
    */
    public function getSpecific() {
        $className = WidgetDescriptor::find($this->descriptor_id)->getClassName();
        return $className::find($this->id);
    }

     /**
     * Getting the position from DB and converting it to an object.
     *
     * @returns Position Object.
    */
    public function getPosition() {
        return json_decode($this->position);
    }

     /**
     * Setting the position of the model.
     *
     * @param array $decoded position from json.
     * @returns string A valid stripe conenct URI.
    */
    public function setPosition(array $decoded_position) {
        $validKeys = array('size_x', 'size_y', 'col', 'row');
        $position = array();

        // Testing json position corruption.
        if ($decoded_position === null) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        // Iterating through the positions.
        foreach($decoded_position as $key=>$value) {
            if (in_array($key, $validKeys)) {
                // There's a match in the array, saving position.
                $position[$key] = $value;
                // Removing key to handle duplications.
                unset($validKeys[array_search($key, $validKeys)]);
            }
        }

        // The valid keys should be empty.
        if (!empty($validKeys)) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        $this->position = json_encode($position);
        $this->save();
    }

    /* -- Eloquent overridden methods -- */
    /**
     * Overriding save to add descriptor automatically.
     *
     * @returns the saved object.
    */
    public function save(array $options=array()) {
        // By default calling general save.
        if (!static::$type) {
            return parent::save();
        }
        // Associating descriptor.
        $clockWidgetDescriptor = WidgetDescriptor::where('type', static::$type)->first();

        // Checking descriptor.
        if ($clockWidgetDescriptor === null) {
            throw new DescriptorDoesNotExist(
                "The '".static::$type."' widget descriptor does not exist. ", 1);
        }

        // Assigning descriptor.
        $this->descriptor()
             ->associate($clockWidgetDescriptor);

        // Calling parent.
        return parent::save();
    }

    /**
     * Overriding all method to filter clock widgets.
     *
     * @returns all the specific widgets.
    */
    public static function all($columns = array('*')) {
        // By default calling general all.
        if (!static::$type) {
            return parent::all();
        }
        return WidgetDescriptor::where('type', static::$type)
                               ->first()->widgets;
   }

    /** Getting the laravel validation array.
     *
     * @param array Fields the fields ti validate.
     * @returns array a laravel validation array.
    */
    public function getSettingsValidationArray($fields) {
        $validationArray = array();

        foreach ($this->getSettingsFields() as $fieldName=>$fieldMeta) {
            // Not validating fields that are not present.
            if (!in_array($fieldName, $fields)) {
                continue;
            }
            $validationString = '';

            // Looking for custom validation.
            if (isset($fieldMeta['validation'])) {
                $validationString .= $fieldMeta['validation']."|";
            }

            // Doing type based validation.
            if ($fieldMeta['type'] == 'SCHOICE') {
                $validationString .= 'in:' . implode(',',array_keys($this->$fieldName()))."|";
            } else if ($fieldMeta['type'] == 'INT') {
                $validationString .= 'integer|';
            } else if ($fieldMeta['type'] == 'FLOAT') {
                $validationString .= 'numeric|';
            } else if ($fieldMeta['type'] == 'DATE') {
                $validationString .= 'date|';
            }

            // Adding validation to the return array.
            if (strlen($validationString) > 0) {
                $validationArray[$fieldName] = rtrim($validationString, '|');
            }
        }

        // Return.
        return $validationArray;
    }

    /** Getting the settings from db, and transforming it to assoc.
     *
     * @returns string widget Type
    */
    public function getSettings() {
        if ($this->settings)
            return json_decode($this->settings, 1);

        // Returning empty settings array, with valid keys.
        $settings = array();
        foreach (array_keys($this->getSettingsFields()) as $fieldName) {
            $settings[$fieldName] = "";
        }
        return $settings;
    }

    /** Transforming settings to JSON format. (validation done by view)
     *
     * @param array $inputSettings the settings array.
     * @returns None
    */
    public function saveSettings($inputSettings) {
        $fields = array_keys($this->getSettingsFields());
        $settings = array();
        $oldSettings = $this->getSettings();

        // Iterating through the positions.
        foreach (array_keys($this->getSettingsFields()) as $fieldName) {
            // inputSettings. oldSettings, empty string.
            if (isset($inputSettings[$fieldName])) {
                $settings[$fieldName] = $inputSettings[$fieldName];
            } else if ($oldSettings[$fieldName]) {
                // Value not set, Getting from old settings.
                $settings[$fieldName] = $oldSettings[$fieldName];
            } else {
                $settings[$fieldName] = "";
            }
        }

        $this->settings = json_encode($settings);
        $this->save();
    }
}
?>