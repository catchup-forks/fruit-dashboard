<?php

class GreetingsWidget extends Widget
{
    /* Greetings widget */
    private static $morningStartsAt   = 5;
    private static $afternoonStartsAt = 13;
    private static $eveningStartsAt   = 17;
    private static $nightStartsAt     = 22;

    /**
     * getTimeOfTheDay:
     * --------------------------------------------------
     * Returns the time of the day string
     * @return (string) ($timeOfTheDay) morning, afternoon, evening, night
     * --------------------------------------------------
     */
    public static function getTimeOfTheDay() {
        /* Get TimeZone aware hour */
        if (Session::get('timeZone')) {
            $hour = Carbon::now(Session::get('timeZone'))->hour;
        /* Error handling (TimeZone is not present, use Server time */
        } else {
            $hour = Carbon::now()->hour;
        }

        /* Morning */
        if ((self::$morningStartsAt <= $hour) and ($hour < self::$afternoonStartsAt)) {
            return 'morning';

        /* Afternoon */
        } elseif ((self::$afternoonStartsAt <= $hour) and ($hour < self::$eveningStartsAt)) {
            return 'afternoon';

        /* Evening */
        } elseif ((self::$eveningStartsAt <= $hour) and ($hour < self::$nightStartsAt)) {
            return 'evening';

        /* Night */
        } else {
            return 'night';
        }
    }
}

?>
