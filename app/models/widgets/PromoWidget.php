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

        if ($descriptor->type == 'google_analytics_goal_completion') {
            /* Check if user has any goals. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('active', TRUE)->first();
            $goal = $profile->goals()->where('active', TRUE)->first();
            if ($goal) {
                $this->transform(array(
                    'profile' => $profile->profile_id,
                    'goal'    => $goal->goal_id
                ));
            }

        } else if ($descriptor->category == 'google_analytics') {
            /* Check if user has any profiles. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('active', TRUE)->first();
            if ($profile) {
                $this->transform(array('profile' => $profile->profile_id));
            }
        } else if ($descriptor->category == 'facebook') {
            /* Check if user has any pages. */
            $page = $this->user()->facebookPages()
                ->where('active', TRUE)->first();
            if ($page) {
                $this->transform(array('page' => $page->id));
            }
        } else if ($descriptor->category == 'twitter') {
            /* Check if user has any twitter users. */
            $twitterUser = $this->user()->twitterUsers()
                ->where('active', TRUE)->first();
            if ($twitterUser) {
                $this->transform();
            }
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
        /* Getting descriptor. */
        $descriptor = WidgetDescriptor::find($this->getSettings()['related_descriptor']);
        $className = $descriptor->getClassName();

        /* Creating new isntance */
        $widget = new $className(array(
            'position' => $this->position,
            'state'    => 'loading'
        ));
        $widget->descriptor_id = $descriptor->id;
        $widget->dashboard()->associate($this->dashboard);

        /* Saving widget, deleting the promo widget */
        $settings = array_merge(
            json_decode($this->getSettings()['widget_settings'], 1),
            $criteria
        );
        $widget->saveSettings($settings);
        $this->delete();
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
}
?>
