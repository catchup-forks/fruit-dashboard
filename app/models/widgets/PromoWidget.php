<?php

class PromoWidget extends Widget
{
    /* -- Settings -- */
    public static $promoSettings = array(
        'related_descriptor' => array(
            'name'       => 'Related descriptor',
            'type'       => 'INT',
            'validation' => 'required',
            'hidden'     => TRUE
        ),
        'widget_settings' => array(
            'name'       => 'Settings of the widget that will be created',
            'type'       => 'string',
            'default'    => '[]',
            'hidden'     => TRUE
        ),
        'photo_location' => array(
            'name'       => 'The location of the photo of the widget.',
            'type'       => 'string',
            'default'    => '',
            'validation' => 'required',
            'hidden'     => TRUE
        )
    );

    /**
     * checkIntegrity
     * Transforming the widget with specific criteria,
     * if the transform condition applies.
    */
    public function checkIntegrity() {
        parent::checkIntegrity();

        $descriptor = WidgetDescriptor::find($this->getSettings()['related_descriptor']);

        if (is_null($descriptor)) {
            /* Invalid descriptor in DB. */
            throw new WidgetFatalException;
        }
        /* Creating criteria based on the service. */
        if ($descriptor->type == 'google_analytics_goal_completion') {
            $criteria = $this->getGoalCriteria();
        } else if ($descriptor->category == 'google_analytics') {
            $criteria = $this->getProfileCriteria();
        } else if ($descriptor->category == 'facebook') {
            $criteria = $this->getFacebookCriteria();
        } else if ($descriptor->category == 'twitter') {
            $criteria = $this->getTwitterCriteria();
        }
        if (isset($criteria)) {
            /* Transforming widget if criteria is set */
            $this->transform($criteria);
        }
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$promoSettings);
     }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'relatedDescriptor' => $this->getRelatedDescriptor()
        ));
    }

    /**
     * transform
     * Transforming this object into a specific one.
     * --------------------------------------------------
     * @param array $criteria
     * --------------------------------------------------
    */
    private function transform(array $criteria=array()) {
        /* Getting and changing descriptor. */
        $descriptor = WidgetDescriptor::find($this->getSettings()['related_descriptor']);
        $this->descriptor_id = $descriptor->id;
        $this->save();

        /* Getting the new object. */
        $widget = Widget::find($this->id);
        $settings = array_merge(
            json_decode($this->getSettings()['widget_settings'], 1),
            $criteria
        );
        $widget->saveSettings($settings);
    }

    /**
     * getRelatedDescriptor
     * Returning the related descriptor.
     * --------------------------------------------------
     * @return WidgetDescriptor
     * --------------------------------------------------
     */
    public function getRelatedDescriptor() {
        return WidgetDescriptor::find($this->getSettings()['related_descriptor']);
    }

    /**
     * getGoalCriteria
     * Returning the criteria a the GA goal widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getGoalCriteria() {
        /* Getting active profile. */
        $profile = $this->user()->googleAnalyticsProfiles()
            ->where('active', TRUE)->first();
        if (is_null($profile)) {
            return NULL;
        }

        /* Getting active goal. */
        $goal = $profile->goals()->where('active', TRUE)->first();
        if (is_null($goal)) {
            return NULL;
        }

        return array(
            'goal'    => $goal->goal_id,
            'profile' => $profile->profile_id
        );
    }

    /**
     * getProfileCriteria
     * Returning the criteria a GA widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getProfileCriteria() {
        /* Getting active profile. */
        $profile = $this->user()->googleAnalyticsProfiles()
            ->where('active', TRUE)->first();
        if (is_null($profile)) {
            return NULL;
        }
        return array(
            'profile' => $profile->profile_id
        );
    }

    /**
     * getFacebookCriteria
     * Returning the criteria a Facebook widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getFacebookCriteria() {
        /* Getting active profile. */
        $page = $this->user()->facebookPages()->where('active', TRUE)->first();
        if (is_null($page)) {
            return NULL;
        }
        return array(
            'page' => $page->id
        );
    }

    /**
     * getTwitterCriteria
     * Returning the criteria a Twitter widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getTwitterCriteria() {
        /* Getting active profile. */
        $twitterUser = $this->user()->twitterUsers()
            ->where('active', TRUE)->first();
        if (is_null($twitterUser)) {
            return NULL;
        }
        return array();
    }
}
?>
