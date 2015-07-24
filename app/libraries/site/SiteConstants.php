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
    private static $gridSizeX       = 12;
    private static $gridSizeY       = 12;
    private static $morningStarts   = 5;
    private static $afternoonStarts = 13;
    private static $eveningStarts   = 17;
    private static $nightStarts     = 22;

    /* -- Constructor -- */
    public function __construct() {
    }

    /**
     * ================================================== *
     *               PUBLIC STATIC SECTION                *
     * ================================================== *
     */

    /**
     * getGridSizeX: 
     * --------------------------------------------------
     * Returns the number of grid X axis slots.
     * @return (integer) ($gridSizeX) gridSizeX
     * --------------------------------------------------
     */
    public static function getGridSizeX() {
        return self::$gridSizeX;
    }

    /**
     * getGridSizeY: 
     * --------------------------------------------------
     * Returns the number of grid X axis slots.
     * @return (integer) ($gridSizeX) gridSizeX
     * --------------------------------------------------
     */
    public static function getGridSizeY() {
        return self::$gridSizeY;
    }

    /**
     * getTimeOfTheDay: 
     * --------------------------------------------------
     * Returns the number of grid X axis slots.
     * @return (string) ($timeOfTheDay) morning, afternoon, evening, night
     * --------------------------------------------------
     */
    public static function getTimeOfTheDay() {
        $hour = Carbon::now()->hour;

        /* Morning */
        if ((self::$morningStarts <= $hour) and ($hour < self::$afternoonStarts)) {
            return 'morning';
        
        /* Afternoon */
        } elseif ((self::$afternoonStarts <= $hour) and ($hour < self::$eveningStarts)) {
            return 'afternoon';

        /* Evening */
        } elseif ((self::$eveningStarts <= $hour) and ($hour < self::$nightStarts)) {
            return 'evening';

        /* Night */
        } else {
            return 'night';
        }
    }
    
} /* SiteConstants */