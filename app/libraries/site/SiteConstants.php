<?php

/**
* -------------------------------------------------------------------------- 
* SiteConstants: 
*       Wrapper functions for the constants.
*       All functions can be called directly from the templates   
* Usage:
*       PHP     | $constant = SiteConstants::functionName();
*       BLADE   | {{ SiteConstants::functionName() }}
* -------------------------------------------------------------------------- 
*/
class SiteConstants {
    /* -- Class properties -- */
    private static $gridNumberOfCols    = 12;
    private static $gridNumberOfRows    = 12;
    private static $morningStartsAt     = 5;
    private static $afternoonStartsAt   = 13;
    private static $eveningStartsAt     = 17;
    private static $nightStartsAt       = 22;
    private static $trialPeriodInDays   = 14;

    /**
     * ================================================== *
     *               PUBLIC STATIC SECTION                *
     * ================================================== *
     */

    /**
     * getGridNumberOfCols: 
     * --------------------------------------------------
     * Returns the number of grid Y axis slots.
     * @return (integer) ($gridNumberOfCols) gridNumberOfCols
     * --------------------------------------------------
     */
    public static function getGridNumberOfCols() {
        return self::$gridNumberOfCols;
    }

    /**
     * getGridNumberOfRows: 
     * --------------------------------------------------
     * Returns the number of grid X axis slots.
     * @return (integer) ($gridNumberOfRows) gridNumberOfRows
     * --------------------------------------------------
     */
    public static function getGridNumberOfRows() {
        return self::$gridNumberOfRows;
    }

    /**
     * getTimeOfTheDay: 
     * --------------------------------------------------
     * Returns the time of the day string
     * @return (string) ($timeOfTheDay) morning, afternoon, evening, night
     * --------------------------------------------------
     */
    public static function getTimeOfTheDay() {
        $hour = Carbon::now()->hour;

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

    /**
     * getTrialPeriodInDays: 
     * --------------------------------------------------
     * Returns the trial period in days.
     * @return (integer) ($trialPeriodInDays) trialPeriodInDays
     * --------------------------------------------------
     */
    public static function getTrialPeriodInDays() {
        return self::$trialPeriodInDays;
    }
    
} /* SiteConstants */