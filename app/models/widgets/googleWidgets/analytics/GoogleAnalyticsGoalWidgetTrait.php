<?php

trait GoogleAnalyticsGoalWidgetTrait
{
    use GoogleAnalyticsWidgetTrait;

    /* -- Settings -- */
    private static $goalSettings = array(
        'goal' => array(
            'name'         => 'Goal',
            'type'         => 'SCHOICE',
            'validation'   => 'required',
            'ajax_depends' => 'profile'
        ),
    );
    private static $goal = array('goal');

    /* Choices functions */
    public function goal($profileId=null) {
        $init = FALSE;
        if ($profileId) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $profileId)
                ->first(array('google_analytics_profiles.id'));
        } else if ($this->getProfileId()) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $this->getProfileId())
                ->first(array('google_analytics_profiles.id'));
        } else {
            /* On init using first profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->orderBy('google_analytics_properties.name')
                ->first(array('google_analytics_profiles.id'));
            $init = TRUE;
        }

        if (is_null($profile)) {
            throw new Exception("The selected profile is invalid.", 1);
        }

        $goals = array();
        foreach ($profile->goals as $goal) {
            $goals[$goal->goal_id] = $goal->name;
        }

        if ( ! $init && empty($goals)) {
            throw new Exception("No goal found, for the selected profile.", 1);
        }

        return $goals;
    }

    /**
     * getSettingsFields
     * --------------------------------------------------
     * Returns the updated settings fields
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields() {
        return array_merge(
            parent::getSettingsFields(),
            self::$profileSettings,
            self::$goalSettings
        );
    }

    /**
     * getSetupFields
     * --------------------------------------------------
     * Updating setup fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getSetupFields() {
        return array_merge(
            parent::getSetupFields(),
            self::$profile,
            self::$goal
        );
    }

    /**
     * getCriteriaFields
     * --------------------------------------------------
     * Updating criteria fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getCriteriaFields() {
        return array_merge(
            parent::getCriteriaFields(),
            self::$profile,
            self::$goal
        );
    }

    /**
     * getServiceSpecificName
     * Return the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getServiceSpecificName() {
        return $this->getProperty()->name . ' - ' . $this->getGoal()->name;
    }

    /**
     * getGoal
     * --------------------------------------------------
     * Return the corresponding goal.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getGoal() {
        $profile = $this->getProfile(array('google_analytics_profiles.id'));
        foreach ($profile->goals as $iGoal) {
            if ($iGoal->goal_id == $this->getSettings()['goal']){
                return $iGoal;
            }
        }
        return null;
    }

}

?>
