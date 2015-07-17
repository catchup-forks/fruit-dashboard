<?php

class Settings extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'data';

    // -- Fields -- //
    protected $fillable = array(
        'newsletter_frequency',
        'background_enabled'
    );

    // -- Relations -- //
    /**
     * Returning the corresponding User object.
     *
     * @return a User object.
    */
    public function user() {
        return $this->belongsTo('User');
    }
}
