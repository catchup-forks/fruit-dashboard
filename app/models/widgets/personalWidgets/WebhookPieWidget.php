<?php

class WebhookPieWidget extends PieWidget
{
    use WebhookWidget;
    /* -- Settings -- */
    public static $settingsFields = array(
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
   );

    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('name', 'url');

    public function getCurrentValue() {
    }

}
?>
