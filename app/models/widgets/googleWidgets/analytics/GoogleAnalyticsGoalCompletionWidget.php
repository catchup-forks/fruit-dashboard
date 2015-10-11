<?php
class GoogleAnalyticsGoalCompletionWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    /* -- Settings -- */
    private static $goalSettings = array(
        'goal' => array(
            'name'       => 'Goal',
            'type'       => 'SCHOICE',
            'validation' => 'required'
        ),
        'profile' => array(
            'name'       => 'Profile',
            'type'       => 'TEXT',
            'validation' => 'required',
            'hidden'     => TRUE
        )
    );

    /* Choices functions */
    public function goal() {
        $goals = array();
        foreach ($this->user()->googleAnalyticsProfiles as $profile) {
            foreach ($profile->goals as $goal) {
                $goals[$goal->goal_id] = $goal->name;
            }
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
        return array_merge(parent::getSettingsFields(), self::$goalSettings);
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
                if ($iGoal == $this->getSettings['goal']){
                    return $goal;
                }
            }
        }
        return null;
    }

    /**
     * save
     * Saving profile automatically.
     * --------------------------------------------------
     * @param array $options
     * @return null
     * --------------------------------------------------
    */
    public function save(array $options=array()) {
        parent::save($options);
        $goal = $this->getGoal();

        if ( ! is_null($goal)) {
            $this->saveSettings(array(
                'profile' => $this->getGoal()->profile->profile_id
            ),
            FALSE);
        }

        return parent::save($options);
    }

}
?>