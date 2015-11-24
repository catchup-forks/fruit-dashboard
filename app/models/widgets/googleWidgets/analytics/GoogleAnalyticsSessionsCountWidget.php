<?php

class GoogleAnalyticsSessionsCountWidget extends Widget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    public static $histogramDescriptor = 'google_analytics_sessions';

    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'propertyName' => $this->getProperty()->name
        ));
    }
}
?>
