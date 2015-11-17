<?php

class PromoWidget extends Widget
{
    /* -- Settings -- */
    public static $promoSettings = array(
        'related_descriptor' => array(
            'name'       => 'Related descriptor',
            'type'       => 'INT',
            'validation' => 'required',
            'hidden'     => true
        ),
        'widget_settings' => array(
            'name'       => 'Settings of the widget that will be created',
            'type'       => 'string',
            'default'    => '[]',
            'hidden'     => true
        ),
        'photo_location' => array(
            'name'       => 'The location of the photo of the widget.',
            'type'       => 'string',
            'default'    => '',
            'validation' => 'required',
            'hidden'     => true
        )
    );

    /**
     * getConnectionMeta
     * Returns the connection meta.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getConnectionMeta() {
        $descriptor = WidgetDescriptor::find($this->getSettings()['related_descriptor']);

        if (is_null($descriptor)) {
            /* Invalid descriptor in DB. */
            throw new DescriptorDoesNotExists;
        }

        $connectionText = "Connect the service";

        if ($descriptor->category == 'google_analytics') {
            /* GA descriptor. */
            if ( ! $this->user()->isServiceConnected('google_analytics')) {
                return array(
                    'text' => $connectionText,
                    'url'  => route('service.google_analytics.connect')
                );
            }

            /* GA connected. */
            if ($descriptor->type == 'google_analytics_goal_completion' ||
                    $descriptor->type == 'google_analytics_conversions') {
                /* Goal widget. */
                return array(
                    'text' => 'Please select your Google Analytics goal',
                    'url'  => route('service.google_analytics.select-properties')
                );
            }
            /* Default GA widget. */
            return array(
                'text' => 'Please select your Google Analytics profile',
                'url'  => route('service.google_analytics.select-properties')
            );
        } else if ($descriptor->category == 'facebook') {
            if ( ! $this->user()->isServiceConnected('facebook')) {
                return array(
                    'text' => $connectionText,
                    'url'  => route('service.facebook.connect')
                );
            }
            /* Facebook connected. */
            return array(
                'text' => 'Please select your Facebook page',
                'url'  => route('service.facebook.select-pages')
            );
        } else {
            return array(
                'text' => $connectionText,
                'url'  => route('service.' . $descriptor->category . '.connect')
            );
        }
    }

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

        /* Create criteria based on the service. */
        if ($descriptor->type == 'google_analytics_goal_completion' ||
                $descriptor->type == 'google_analytics_conversions') {
            $criteria = $this->getGoalCriteria();
        } else if ($descriptor->category == 'google_analytics') {
            $criteria = $this->getProfileCriteria();
        } else if ($descriptor->category == 'facebook') {
            $criteria = $this->getFacebookCriteria();
        } else if ($descriptor->category == 'twitter') {
            $criteria = $this->getTwitterCriteria();
        } else if ($this->user()->isServiceConnected($descriptor->category)) {
            $criteria = array();
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
    public static function getSettingsFields()
    {
        return array(self::$promoSettings);
    }


    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'relatedDescriptor' => $this->getRelatedDescriptor(),
            'connectionMeta'    => $this->getConnectionMeta(),
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
        try {
            $widget->checkIntegrity();
        } catch (WidgetException $e) {
            $widget->save();
        }
    }

    /**
     * getRelatedDescriptor
     * Return the related descriptor.
     * --------------------------------------------------
     * @return WidgetDescriptor
     * --------------------------------------------------
     */
    public function getRelatedDescriptor() {
        return WidgetDescriptor::find($this->getSettings()['related_descriptor']);
    }

    /**
     * getGoalCriteria
     * Return the criteria a the GA goal widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getGoalCriteria() {
        /* Getting active profile. */
        $profile = $this->user()->googleAnalyticsProfiles()
            ->where('active', true)
            ->first(array(
                'google_analytics_profiles.id',
                'google_analytics_profiles.profile_id'
            ));
        if (is_null($profile)) {
            return null;
        }

        /* Getting active goal. */
        $goal = $profile->goals()->where('active', true)->first();
        if (is_null($goal)) {
            return null;
        }

        return array(
            'goal'    => $goal->goal_id,
            'profile' => $profile->profile_id
        );
    }

    /**
     * getProfileCriteria
     * Return the criteria a GA widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getProfileCriteria() {
        /* Getting active profile. */
        $profile = $this->user()->googleAnalyticsProfiles()
            ->where('active', true)->first();
        if (is_null($profile)) {
            return null;
        }
        return array(
            'profile' => $profile->profile_id
        );
    }

    /**
     * getFacebookCriteria
     * Return the criteria a Facebook widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getFacebookCriteria() {
        /* Getting active profile. */
        $page = $this->user()->facebookPages()->where('active', true)->first();
        if (is_null($page)) {
            return null;
        }
        return array(
            'page' => $page->id
        );
    }

    /**
     * getTwitterCriteria
     * Return the criteria a Twitter widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getTwitterCriteria() {
        /* Getting active profile. */
        $twitterUser = $this->user()->twitterUsers()
            ->where('active', true)->first();
        if (is_null($twitterUser)) {
            return null;
        }
        return array();
    }
}
?>
