<?php

class StripeMrrWidget extends Widget
{
    /* -- Table specs -- */
    public static $type = 'stripe_mrr';

    /* -- Settings -- */
    public static $settingsFields = array(
        'graph_on_off' => array('name' => 'Graph on/off', 'type' => 'SCHOICE', 'validation' => 'required'),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array('graph_on_off');

    /* Choices functions */
    public function graph_on_off() {
        return array(
            0 => 'Off',
            1 => 'On'
        );
    }

    public function getData() {
        return 12;
    }
}

?>
