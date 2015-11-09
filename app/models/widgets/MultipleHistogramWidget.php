<?php

abstract class MultipleHistogramWidget extends HistogramWidget
{
    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    public function type() {
        $types = array('chart' => 'Chart');
        return $types;
    }
}
?>
