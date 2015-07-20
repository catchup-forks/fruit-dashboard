<?php

class ClockWidget extends Widget
{
    // -- Table specs -- //
    protected $table = "widgets";

    /**
     * Overriding save to add descriptor automatically.
     *
     * @returns the saved object.
    */
    public function save(array $options=array()) {
        // Associating descriptor.
        $clockWidgetDescriptor = WidgetDescriptor::where('type', 'clock')->first();

        // Checking descriptor.
        if ($clockWidgetDescriptor === null) {
            throw new DescriptorDoesNotExist(
                "The 'Clock' widget descriptor does not exist. ", 1);
        }

        // Assigning descriptor.
        $this->descriptor()
             ->associate($clockWidgetDescriptor);

        // Calling parent.
        return parent::save();
    }
}

?>
