<?php

class Background extends Eloquent
{
    /* Escaping eloquent's plural naming. */
    protected $table = 'backgrounds';

    /* -- Fields -- */
    protected $fillable = array(
        'is_enabled',
        'number',
        'url',
    );
    public $timestamps = FALSE;

    /* -- Relations -- */
    public function user() { return $this->belongsTo('User'); }

    /* -- Static variables -- */
    private static $numOfBackgroundFiles    = null;
    private static $backgroundFiles         = null;

    /**
     * ================================================== *
     *               PUBLIC STATIC SECTION                *
     * ================================================== *
     */

    /**
     * getBaseNumber
     * --------------------------------------------------
     * @return (integer) ($baseNumber) The base picture number
     * --------------------------------------------------
     */
    public static function getBaseNumber() {
        return $imageNumber = Carbon::now()->dayOfYear % self::$numOfBackgroundFiles;
    }

    /**
     * getBaseUrl
     * --------------------------------------------------
     * @return (string) ($baseUrl) The base picture url
     * --------------------------------------------------
     */
    public static function getBaseUrl() {
        return self::$backgroundFiles[self::getBaseNumber()];
    }

    /**
     * getBackgroundFiles
     * --------------------------------------------------
     * @return Initializes the backgroundFiles static variables
     * --------------------------------------------------
     */
    public static function getBackgroundFiles() {
        /* On production server proceed with backgrounds-production directory.
         * Otherwise get the urls from backgrounds directory.
         * Backgrounds-production is too large to be included in the git repository
         */

        /* Check environment */
        $relDir = '/img/backgrounds-production/';
        $absDir = public_path() . $relDir;
        if ((!file_exists($absDir)) or (!App::environment('production'))) {
            $relDir = '/img/backgrounds/';
            $absDir = public_path() . $relDir;
        }

        /* get the number of background images & collect them in an array */
        $i = 0;
        self::$backgroundFiles = array();
        
        /* Iterate through files and add them to the list */
        if ($handle = opendir($absDir)) {
            while (($filename = readdir($handle)) !== false) {
                if (!in_array($filename, array('.', '..')) && 
                    !is_dir($absDir. $filename) && 
                    !(substr($filename, 0, 1 ) === ".")) {
                        self::$backgroundFiles = array_add(self::$backgroundFiles, $i, $relDir.$filename);
                        $i++;
                }
            }
        }

        /* Set numOfBackgroundFiles */
        self::$numOfBackgroundFiles = $i;
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * changeUrl
     * --------------------------------------------------
     * @return Changes the URL of the user
     * --------------------------------------------------
     */
    public function changeUrl() {     
        /* URL already exists, get the next */
        if ($this->url != null) {
            /* Check for overflow */
            if ($this->number + 1 >= self::$numOfBackgroundFiles) {
                /* Get the first picture */
                $this->number = ($this->number + 1 - self::$numOfBackgroundFiles) % self::$numOfBackgroundFiles;
            } else {
                /* get the next picture */
                $this->number += 1;
            }
            
            /* Get url and save */
            $this->url = self::$backgroundFiles[$this->number];
            $this->save();

        /* URL does not exist, get the actual base url */
        } else {
            /* Get the day number in the year */
            $this->number = self::getBaseNumber();
            $this->url = self::$backgroundFiles[$this->number];
            $this->save();
        }
        /* Return */
        return TRUE;
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* Background */

/* Initialize backgrounds */
Background::getBackgroundFiles();
