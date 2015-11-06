<?php

class WebhookHistogramWidget extends DataWidget
{
    /* Data selector. */
    protected static $dataTypes = array('webhook');

    /* Data format definer. */
    use NumericWidgetTrait;

    /* Histogram layout data handler.  */
    use HistogramWidgetTrait;

    /* Chart data transformer. */
    use MultipleChartWidgetTrait;

    /* -- Settings -- */
    private static $webhookSettings = array(
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be populated from data on this url.'
        ),
    );

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields()
    {
        return array(
            'Chart settings'   => static::$chartSettings,
            'Webhook settings' => static::$webhookSettings
        );
    }

    /**
     * getSetupFields
     * Returns the SetupFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), array('url'));
     }

    /**
     * getCriteriaFields
     * Returns the CriteriaFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getSetupFields(), array('url'));
     }

    /**
     * getTemplateData
     * Return all values used in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateData()
    {
        return array_merge(parent::getTemplateData(), array(
            'data'          => $this->buildChartData(),
            'currentDiff'   => array(0),
            'currentValue'  => array(0),
            'format'        => $this->getFormat(),
        ));
    }

    /**
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function buildChartData()
    {
        /* Building the histograms. */
        $this->setActiveHistogram($this->transformToSingle($this->data['webhook']['data']));
        return $this->getChartJSData('Y-m-d', TRUE);
    }
}
?>
