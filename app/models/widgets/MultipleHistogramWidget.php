<?php

abstract class MultipleHistogramWidget extends HistogramWidget
{
    /**
     * groupDataSets
     * Returning grouped histogram.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public function groupDataSets() {
        return $this->data->manager->getSpecific()->groupDataSets();
     }
}
?>
