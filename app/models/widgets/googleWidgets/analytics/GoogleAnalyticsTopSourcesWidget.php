<?php

class GoogleAnalyticsTopSourcesWidget extends DataWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('new_users', 'sessions');

    /* Table layout data handler.  */
    use TableWidgetTrait;

    /* Histogram data handler.  */
    use HistogramDataTrait;

    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

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
            'Google Analytics Settings' => static::$profileSettings
        );
    }
}
?>
