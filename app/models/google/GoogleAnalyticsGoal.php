<?php

class GoogleAnalyticsGoal extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'goal_id',
        'profile_id'
    );

    // -- Options -- //
    public $timestamps = FALSE;

    // -- Relations -- //
    public function profile() { return $this->belongsTo('GoogleAnalyticsProfile', 'profile_id'); }
    public function property() { return $this->profile->property; }

    /**
     * getMetricName
     * The metric name used in google analytics api requests.
     * --------------------------------------------------
     * @param string $metric
     * @return string
     * --------------------------------------------------
     */
    public function getGoalPrefix($metric) {
        return 'goal' . $this->goal_id . $metric;
    }
}
