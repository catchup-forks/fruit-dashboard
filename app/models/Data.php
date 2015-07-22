<?php

class Data extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'data';

    // -- Fields -- //
    protected $fillable = array('raw_value');
}

?>