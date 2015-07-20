<?php

class Widget extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'state',
        'settings',
        'position'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function descriptor() { return $this->belongsTo('WidgetDescriptor'); }
    public function data() { return $this->hasOne('Data'); }
    public function dashboard() { return $this->belongsTo('Dashboard'); }
    public function user() { return $this->dashboard->user; }

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

}
?>