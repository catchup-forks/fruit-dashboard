<?php

class QuoteWidget extends CronWidget implements iAjaxWidget
{
    /* -- Settings -- */
    private static $quoteSettings = array(
        'type' => array(
            'name'    => 'Type',
            'type'    => 'SCHOICE',
            'default' => 'inspirational',
        ),
    );

    private static $typeSettings = array('type');

    /* Choices functions */
    public function type() {
        return array(
            'inspirational' => 'Inspirational',
            'funny'         => 'Funny',
            'first-line'    => 'First lines from books',
        );
    }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'data' => $this->getData()
        ));
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

    /**
     * getCriteriaFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getCriteriaFields(), self::$typeSettings);
     }

    /**
     * getSetupFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$typeSettings);
     }
}

?>
