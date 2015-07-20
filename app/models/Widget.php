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
    public function getPosition() {
        return json_decode($this->position, TRUE);
    }
     /**
     * Setting the position of the model.
     *
     * @param string (json) $json_position.
     * @returns string A valid stripe conenct URI.
    */

    public function setPosition($json_position) {
        // Testing json position corruption.
        $decoded_string = json_decode($json_position);
        if ($decoded_string === null) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        $this->position = $json_position;
        $this->save();
    }

}
?>