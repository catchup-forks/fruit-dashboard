<?php

abstract class MultipleHistogramWidget extends HistogramWidget
{
    public function type() {
        $types = array('chart' => 'Chart');
        return $types;
    }
}
?>
