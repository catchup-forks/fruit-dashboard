<?php

class QuoteWidget extends CronWidget implements iAjaxWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'type' => array(
            'name'    => 'Type',
            'type'    => 'SCHOICE',
        ),
    );

    /* The settings to setup in the setup-wizard */
    public static $setupSettings = array('type');
    public static $criteriaSettings = array('type');

    /* Choices functions */
    public function type() {
        return array(
            'inspirational' => 'Inspirational',
            'funny'         => 'Funny',
            'first-line'    => 'First lines from books',
        );
    }
}

?>
