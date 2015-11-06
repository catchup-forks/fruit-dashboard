<?php

trait NumericWidgetTrait
{
    protected static $format = '%d';

    /**
     * getFormat
     * Return the string format of the data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getFormat() {
        return static::$format;
    }

}

?>
