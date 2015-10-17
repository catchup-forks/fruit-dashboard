<?php

class GoogleAnalyticsConversionsWidget extends ServiceTableWidget implements iServiceWidget
{
    use GoogleAnalyticsGoalWidgetTrait;

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'header'  => $this->getHeader(),
            'content' => $this->getContent()
        ));
    }

    /**
     * updateData
     * Refreshing the widget data.
     * --------------------------------------------------
     * @param array options
     * @return string
     * --------------------------------------------------
    */
    public function updateData(array $options=array()) {
        if (empty($options)) {
            $options = array(
                'start'       => $this->getSettings()['range_start'],
                'end'         => $this->getSettings()['range_end'],
                'max_results' => $this->getSettings()['max_results'],
            );
        }
        $this->data->collect($options);
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
        $oldSettings = $this->getSettings();
        parent::saveSettings($inputSettings, $commit);
        if ($oldSettings && $inputSettings &&
                $inputSettings != $oldSettings &&
                $this->dataExists()) {
            $this->updateData();
        }
    }

}
?>
