<?php

abstract class TableWidget extends DataWidget
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
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'name' => $this->getName(),
        ));
    }


    /**
     * getHeader
     * Returning the table header
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getHeader() {
        return $this->dataManager->getHeader();
    }

    /**
     * getContent
     * Returning the table articles.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getContent() {
        return $this->dataManager->getContent();
    }

    /**
     * getName
     * Returning the name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    protected function getName() {
        $name = '';
        if ($this instanceof iServiceWidget && $this->hasValidCriteria()) {
            $name = $this->getServiceSpecificName();
        }
        $name .= ' ' . $this->getSettings()['name'];
        return $name;
    }

    /**
     * saveSettings
     * Collecting new data on change.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) {
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if ($this->getSettings()['name'] == '') {
            $this->saveSettings(array('name' => $this->getDescriptor()->name), $commit);
        }
        return $changedFields;
    }
}

?>
