<?php

class GoogleAnalyticsSessionsCountWidget extends CountWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $histogramDescriptor = 'google_analytics_sessions';

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
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