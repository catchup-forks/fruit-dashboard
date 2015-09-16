<?php

class QuoteWidget extends CronWidget implements iAjaxWidget
{
    /* -- Settings -- */
    public static $quoteSettings = array(
        'type' => array(
            'name'    => 'Type',
            'type'    => 'SCHOICE',
            'default' => 'inspirational'
        ),
    );

    /* Choices functions */
    public function type() {
        return array(
            'inspirational' => 'Inspirational',
            'funny'         => 'Funny',
            'first-line'    => 'First lines from books',
        );
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$quoteSettings);
     }
}

?>
