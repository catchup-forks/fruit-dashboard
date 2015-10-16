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
        if ($profileId) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $profileId)
                ->first();
        } else if ($this->getProfileId()) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $this->getProfileId())
                ->first();
        } else {
            /* On init using first profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->first();
        }

        if (is_null($profile)) {
            throw new Exception("The selected profile is invalid.", 1);
        }

        foreach ($profile->goals as $goal) {
            $goals[$goal->goal_id] = $goal->name;
        }

        if (empty($goals)) {
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
            parent::getSetupFields(),
            self::$profile,
            self::$goal
        );
    }

    /**
     * getDefaultName
     * Returning the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getDefaultName() {
        return $this->getProperty()->name . ', goal:' . $this->getGoal()->name;
    }

    /**
     * getGoal
     * --------------------------------------------------
     * Returning the corresponding goal.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getGoal() {
        $profile = $this->getProfile();
        foreach ($profile->goals as $iGoal) {
            if ($iGoal->goal_id == $this->getSettings()['goal']){
                return $iGoal;
            }
        }
        return null;
    }

}

?>
