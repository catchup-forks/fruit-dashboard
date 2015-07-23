<?php

class QuoteWidget extends Widget
{
    public static $type = 'quote';

    /* -- Settings -- */
    public static $settingsFields = array(
        'type' => array(
            'name'       => 'Type',
            'type'       => 'SCHOICE',
            'validation' => 'required'),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array('type');

    /* Choices functions */
    public function type() {
        return array(
            'insp'       => 'Inspirational',
            'other'      => 'Other'
        );
    }
}

?>
