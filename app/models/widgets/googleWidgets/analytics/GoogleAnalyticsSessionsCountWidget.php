<?php

class GoogleAnalyticsSessionsCountWidget extends CountWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $histogramDescriptor = 'google_analytics_sessions';

    /* -- Settings -- */
    public static $settingsFields = array(
        'period' => array(
            'name'       => 'Period',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days'
        ),
        'multiplier' => array(
            'name'       => 'Number of periods',
            'type'       => 'INT',
            'validation' => 'required',
            'default'    => '1'
        ),
        'property' => array(
            'name'       => 'Property',
            'type'       => 'SCHOICE',
            'validation' => 'required'
        )
    );
    public static $setupSettings = array('property');
    public static $criteriaSettings = array('property');

}
?>