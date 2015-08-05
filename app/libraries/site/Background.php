<?php

/**
* -------------------------------------------------------------------------- 
* Background: 
*       Handles the background related functions
*       All functions can be called directly from the templates   
* Usage:
*       PHP     | $url = Background::dailyBackgroundURL();
*       BLADE   | {{ Background::dailyBackgroundURL() }}
* -------------------------------------------------------------------------- 
*/
class Background {

    /**
     * ================================================== *
     *                PUBLIC STATIC SECTION               *
     * ================================================== *
     */

    /**
     * dailyBackgroundURL:
     * -------------------------------------------------- 
     * Returns the url of the daily background.
     * @return (string) ($dailyBackgroundURL) The background url.
     * --------------------------------------------------
     */
    public static function dailyBackgroundURL() {

        /* Get the day number in the year */
        $numberOfDayInYear = Carbon::now()->dayOfYear;

        # if there is backgrounds-production directory, go with that, otherwise go with backgrounds
        # (backgrounds-production is too large to be included in the git repository)

        $directory = '/img/backgrounds-production/';
        if (!file_exists(public_path().$directory)) {
            $directory = '/img/backgrounds/';
        }

        # get the number of background images & collect them in an array
        $i = 0;
        $fileListArray = array();
        $dir = public_path().$directory;

        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false){
                if (!in_array($file, array('.', '..')) && !is_dir($dir.$file) && !(substr($file, 0, 1 ) === ".")) {
                    $fileListArray = array_add($fileListArray, $i, $file);
                    $i++;
                }
            }
        }
        $numberOfBackgroundFiles = $i;

        # calculate which image will we use
        $imageNumber = $numberOfDayInYear % $numberOfBackgroundFiles;

        # create the url that will be passed to the view
        $imageName = $fileListArray[$imageNumber];
        $dailyBackgroundURL = $directory.$imageName;

        return $dailyBackgroundURL;
    }
} /* Background */
