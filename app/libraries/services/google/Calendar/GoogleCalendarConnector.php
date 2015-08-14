<?php

/**
* --------------------------------------------------------------------------
* GoogleCalendarConnector:
*   Connecting google's analytics service
* --------------------------------------------------------------------------
*/

class GoogleCalendarConnector extends GoogleConnector {
    protected static $service = 'google_calendar';
    protected static $scope = Google_Service_Calendar::CALENDAR_READONLY;
}