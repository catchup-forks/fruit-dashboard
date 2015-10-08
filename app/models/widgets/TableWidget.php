<?php

abstract class TableWidget extends CronWidget
{
    /* -- Settings -- */
    private static $tableSettings = array(
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the widget.'
        ),
    );

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$tableSettings);
     }

    /**
     * getHeader
     * Returning the table header
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getHeader() {
        return $this->dataManager()->getHeader();
    }

    /**
     * getContent
     * Returning the table articles.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getContent() {
        return $this->dataManager()->getContent();
    }

    /**
     * save
     * Adding name property if not set.
     * --------------------------------------------------
     * @param array $options
     * @return null
     * --------------------------------------------------
    */
    public function save(array $options=array()) {
        parent::save($options);
        if ($this instanceof iServiceWidget && $this->hasValidCriteria()) {
            $this->saveSettings(array('name' => $this->getDefaultName()), FALSE);
        }

        return parent::save($options);
    }
}

?>
