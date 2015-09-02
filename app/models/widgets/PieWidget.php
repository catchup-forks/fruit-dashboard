<?php

abstract class PieWidget extends DataWidget implements iCronWidget
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
    */
    abstract public function getCurrentValue();

    /**
     * collectData
     * Getting the new value based on getCurrentValue()
     */
    public function collectData() {}

    /**
     * getSumInRange
     * Returning each dataset summarized in the dates.
     * @return Array
     */
    public function getSumInRange($start, $stop) {}

    /**
     * getLatestData
     * Returning the latest daily data.
     */
    public function getLatestData() {}
}
?>
