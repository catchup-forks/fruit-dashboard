<?php
class GoogleAnalyticsGoalCompletionWidget extends HistogramWidget implements iServiceWidget
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

    private static $setup_criteria = array('profile', 'goal');

    /* Choices functions */
    public function goal($profileId=null) {
        if (is_null($profileId)) {
            /* On init using first profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->first();
        } else {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $profileId)
                ->first();
        }

        if (is_null($profile)) {
            throw new Exception("The selected profile is invalid.", 1);
        }

        foreach ($profile->goals as $goal) {
            $goals[$goal->goal_id] = $goal->name;
        }

        if (empty($goals)) {
            throw new Exception("No goal found.", 1);
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
        return array_merge(parent::getSetupFields(), self::$setup_criteria);
    }

    /**
     * getCriteriaFields
     * --------------------------------------------------
     * Updating criteria fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getCriteriaFields() {
        return array_merge(parent::getSetupFields(), self::$setup_criteria);
    }

    /**
     * getGoal
     * --------------------------------------------------
     * Returning the corresponding goal.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getGoal() {
        foreach ($this->user()->googleAnalyticsProfiles as $profile) {
            foreach ($profile->goals as $iGoal) {
                if ($iGoal == $this->getSettings()['goal']){
                    return $goal;
                }
            }
        }
        return null;
    }
}
?>