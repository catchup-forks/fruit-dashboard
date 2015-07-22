<?php

abstract class Widget extends Eloquent
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

    // This variable will be overwritten, with late static binding.
    public static $type = null;

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
    public function getTyoe() {
        return static::$type;
    }

    // -- Positioning --//
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
        $valid_keys = array('size_x', 'size_y', 'col', 'row');
        $position = array();

        // Testing json position corruption.
        if ($decoded_position === null) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        // Iterating through the positions.
        foreach($decoded_position as $key=>$value) {
            if (in_array($key, $valid_keys)) {
                // There's a match in the array, saving position.
                $position[$key] = $value;
                // Removing key to handle duplications.
                unset($valid_keys[array_search($key, $valid_keys)]);
            }
        }

        // The valid keys should be empty.
        if (!empty($valid_keys)) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        $this->position = json_encode($position);
        $this->save();
    }

    // -- Overridden methods -- //
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
     * @returns all the clock widgets.
    */
    public static function all($columns = array('*')) {
        // By default calling general all.
        if (!static::$type) {
            return parent::all();
        }
        return WidgetDescriptor::where('type', static::$type)
                               ->first()->widgets();
   }

}
?>