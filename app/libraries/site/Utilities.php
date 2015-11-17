<?php

/**
* --------------------------------------------------------------------------
* Utilities:
*       Wrapper functions for the utilities used all over the site.
*       All functions can be called directly from the templates
* Usage:
*       PHP     | $constant = SiteConstants::functionName();
*       BLADE   | {{ SiteConstants::functionName() }}
* --------------------------------------------------------------------------
*/
class Utilities {

    /**
     * underscoreToCamelCase
     * Return a string in CamelCase.
     * --------------------------------------------------
     * @param string $input
     * @param boolean $keepSpace
     * @return string
     * --------------------------------------------------
    */
    public static function underscoreToCamelCase($input, $keepSpace=false) {
        $output = ucwords(str_replace('_',' ', $input));
        return $keepSpace ? $output : str_replace(' ', '', $output);
    }

    /**
     * dashToCamelCase
     * Returns a string in CamelCase.
     * --------------------------------------------------
     * @param string $input
     * @param boolean $keepSpace
     * @return string
     * --------------------------------------------------
    */
    public static function dashToCamelCase($input, $keepSpace=false) {
        $output = ucwords(str_replace('-',' ', $input));
        return $keepSpace ? $output : str_replace(' ', '', $output);
    }

    /**
     * formatNumber
     * Formatting a number based on parameters.
     * --------------------------------------------------
     * @param numeric $input
     * @param boolean $currency
     * @return string
     * --------------------------------------------------
    */
    public static function formatNumber($input, $format='%d') {
        return sprintf($format, $input);
    }

    /**
     * classUses
     * Fixing PHP's failure to return the traits of all
     * parents in class_uses
     * --------------------------------------------------
     * @param string $class
     * @param string $trait
     * @param boolean $autoload
     * --------------------------------------------------
     */
    public function classUses($class, $trait, $autoload=true) {
        $traits = [];

        /* Get traits of all parent classes. */
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        /* Get traits of all parent traits. */
        $traitsToSearch = $traits;
        while ( ! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return in_array($trait, $traits);
    }
}
