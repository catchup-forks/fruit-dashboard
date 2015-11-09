<?php

class Settings extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'settings';

    // -- Fields -- //
    protected $fillable = array(
        'last_activity',
        'api_key',
        'onboarding_state',
        'startup_type',
        'project_name',
        'project_url',
        'company_size',
        'company_funding'
    );
    public $timestamps = false;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
