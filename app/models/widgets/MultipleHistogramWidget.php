<?php

abstract class MultipleHistogramWidget extends HistogramWidget
{
    public function __call($method_name, $args) {
       Log::info('Calling method '.$method_name.'<br />');
    }
}
?>
