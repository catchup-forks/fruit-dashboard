<?php

class Settings extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'settings';

    // -- Fields -- //
    protected $fillable = array(
        'newsletter_frequency',
        'background_enabled'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
