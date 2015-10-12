<?php

class Settings extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'settings';

    // -- Fields -- //
    protected $fillable = array(
        'last_activity',
        'api_key',
        'startup_type',
        'onboarding_state',
        'project_name',
        'project_url',
        'company_size',
        'company_funding'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function user() { return $this->belongsTo('User'); }
}
