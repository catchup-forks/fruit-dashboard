<?php
/* Main widget class */
class Widget extends Eloquent
{
    /* -- Table specs -- */
    protected $table = "widgets";

    /* -- Fields -- */
    protected $fillable = array(
        'state',
        'settings',
        'position'
    );
    public $timestamps = FALSE;

    /* These variables will be overwritten, with late static binding. */
    protected static $settingsFields   = array();
    protected static $setupSettings    = array();
    protected static $criteriaSettings = array();

    /* Use only for association. */
    public function descriptor() { return $this->belongsTo('WidgetDescriptor', 'descriptor_id');}

    /* -- Relations -- */
    public function data() { return $this->belongsTo('Data', 'data_id'); }
    public function dashboard() { return $this->belongsTo('Dashboard'); }
    public function user() {
        return User::remember(120)->find($this->dashboard->user_id);
    }

    /* Optimized method, not using DB query */
    public function getDescriptor() {
        return WidgetDescriptor::find($this->descriptor_id);
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getMinRows
     * Returning the minimum rows required for the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getMinRows() {
        return $this->getDescriptor()->min_rows;
    }

    /**
     * getMinCols
     * Returning the minimum rows required for the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getMinCols() {
        return $this->getDescriptor()->min_cols;
    }

    /**
     * renderable
     * Returns whether or not the widget is renderable.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function renderable() {
        if ($this->state == 'active') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * canSendInNotification
     * --------------------------------------------------
     * Returns whether the widget can be sent in notification or not
     * @return (boolean) ($) true/false
     * --------------------------------------------------
     */
    public function canSendInNotification() {
        return !(in_array($this->getDescriptor()->category, SiteConstants::getSkippedCategoriesInNotification()));
    }

    /**
     * checkIntegrity
     * Checking the widgets settings integrity.
    */
    public function checkIntegrity() {
        /* By default we give a chance to recover from rendering_error */
        if ($this->state == 'rendering_error') {
            $this->setState('active');
        }
        $this->checkSettingsIntegrity();
    }

    /**
     * getSettingsFields
     * Getting the settings meta.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public static function getSettingsFields() {
        return self::$settingsFields;
    }

    /**
     * getErrorCodes
     * Returning the error codes.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public static function getErrorCodes() {
        return array();
    }

    /**
     * getSetupFields
     * Getting the setup settings meta.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public static function getSetupFields() {
        return self::$setupSettings;
    }

    /**
     * getCriteriaFields
     * Returns the criteria settings.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public static function getCriteriaFields() {
        return self::$criteriaSettings;
    }

    /**
     * getPosition
     * Getting the position from DB and converting it to an object.
     * --------------------------------------------------
     * @return Position Object.
     * --------------------------------------------------
    */
    public function getPosition() {
        return json_decode($this->position);
    }

    /**
     * getTemplateMeta
     * Returning data for the gridster init template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateMeta() {
        $position = $this->getPosition();
        return array(
            'general' => array(
                'id'    => $this->id,
                'type'  => $this->getDescriptor()->type,
                'state' => $this->state,
                'row'   => $position->row,
                'col'   => $position->col,
                'sizex' => $position->size_x,
                'sizey' => $position->size_y
            ),
            'features' => array(
                'drag' => true
            ),
            'urls' => array(
              'deleteUrl' => route('widget.delete', $this->id),
              'postUrl'   => route('widget.ajax-handler', $this->id) // AjaxWidgeTrait
            ),
            'selectors' => array(
                'widget'  => '[data-id=' . $this->id . ']',
                'wrapper' => '#widget-wrapper-' . $this->id,
                'loading' => '#widget-loading-' . $this->id,
                'refresh' => '#widget-refresh-' . $this->id,
            ),
            'data' => array(
                'page' => 'dashboard',
                'init' => 'widgetData' . $this->id
            )
        );
    }

    /**
     * getDefaultTemplateData
     * Returning all meta data about the widget.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public static function getDefaultTemplateData($widget) {
        return array(
            'settings'   => $widget->getSettings(),
            'instance'   => $widget,
            'id'         => $widget->id,
            'state'      => $widget->state,
            'position'   => $widget->getPosition(),
            'descriptor' => $widget->getDescriptor()
        );
    }

    /**
     * getTemplateData
     * Returning all data that should be passed to the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateData() {
        return self::getDefaultTemplateData($this);
    }

    /**
     * getSettings
     * Getting the settings from db, and transforming it to assoc.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getSettings() {
        $settings = json_decode($this->settings, 1);
        if ( ! is_array($settings)) {
            return array();
        }
        return $settings;
    }

    /**
     * getCriteria
     * Returning the settings that makes a difference among widgets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getCriteria() {
        $settings = array();
        foreach (static::getCriteriaFields() as $key) {
            Log::info($key);
            Log::info($this->getSettings());
            if (array_key_exists($key, $this->getSettings())) {
                $settings[$key] = $this->getSettings()[$key];
            } else {
                return array();
            }
        }
        return $settings;
    }

    /**
     * setState
     * Setting a widget's state.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
    */
    public function setState($state, $commit=TRUE) {
        if ($this->state == $state) {
            return;
        }
        $this->state = $state;
        if ($commit) {
            $this->save();
        }
    }

    /**
     * isSettingVisible
     * Checking if the given field is visible.
     * --------------------------------------------------
     * @param string $fieldName
     * @return bool
     * --------------------------------------------------
    */
    public function isSettingVisible($fieldName) {
        $settingsFields = $this->getSettingsFields();
        if ( ! array_key_exists($fieldName, $settingsFields)) {
            /* Key doesn't exist. Don't even try to render it. */
            return FALSE;
        }
        $fieldMeta = $settingsFields[$fieldName];
        if (array_key_exists('hidden', $fieldMeta) &&
                $fieldMeta['hidden'] == TRUE) {
            return FALSE;
        }

        if (array_key_exists('ajax', $fieldMeta) &&
                $fieldMeta['ajax'] == TRUE) {
            return TRUE;
        }

        /* Don't show singleChoice if there's only one value */
        if (($fieldMeta['type'] == 'SCHOICE' ||
                $fieldMeta['type'] == 'SCHOICEOPTGRP') &&
                 count($this->$fieldName()) == 1) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * setPosition
     * Setting the position of the model.
     * --------------------------------------------------
     * @param array $decodedPosition
     * --------------------------------------------------
    */
    public function setPosition(array $decodedPosition) {
        $validKeys = array('size_x', 'size_y', 'col', 'row');
        $position = array();

        // Testing json position corruption.
        if ($decodedPosition === null) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        // Iterating through the positions.
        foreach($decodedPosition as $key=>$value) {
            if (in_array($key, $validKeys)) {
                // There's a match in the array, saving position.
                $position[$key] = $value;
                // Removing key to handle duplications.
                unset($validKeys[array_search($key, $validKeys)]);
            }
        }

        // The valid keys should be empty.
        if (!empty($validKeys)) {
            throw new BadPosition("Invalid json postion value: $json_position", 1);
        }

        $this->position = json_encode($position);
        $this->save();
    }

    /**
     * getSettingsValidationArray
     * Getting the laravel validation array.
     * --------------------------------------------------
     * @param array $fields
     * @param array $data
     * @return array
     * --------------------------------------------------
    */
    public function getSettingsValidationArray(array $fields, array $data) {
        $validationArray = array();

        foreach ($this->getSettingsFields() as $fieldName=>$fieldMeta) {
            // Not validating fields that are not present.
            if (!in_array($fieldName, $fields) || ! $this->isSettingVisible($fieldName)) {
                continue;
            }
            $validationString = '';

            // Looking for custom validation.
            if (isset($fieldMeta['validation'])) {
                $validationString .= $fieldMeta['validation']."|";
            }

            // Doing type based validation.
            switch ($fieldMeta['type']) {
                case 'SCHOICE':
                    if (array_key_exists('ajax_depends', $fieldMeta)) {
                        try {
                            $choices = array_keys($this->$fieldName($data[$fieldMeta['ajax_depends']]));
                        } catch (Exception $e) {
                            $choices = array();
                        }
                    } else {
                        $choices = array_keys($this->$fieldName());
                    }
                    $validationString .= 'in:' . implode(',', $choices)."|"; break;
                case 'INT': $validationString .= 'integer|'; break;
                case 'FLOAT':  $validationString .= 'numeric|'; break;
                case 'DATE':  $validationString .= 'date|'; break;
                case 'BOOL':  $validationString .= ''; break;
                case 'SCHOICEOPTGRP':
                    $validValues = array();
                    foreach ($this->$fieldName() as $optGroup) {
                        foreach (array_keys($optGroup) as $key) {
                            array_push($validValues, $key);
                        }
                    }
                    $validationString .= 'in:' . implode(',', $validValues)."|";
                    break;
                default:;
            }

            // Adding validation to the return array.
            if (strlen($validationString) > 0) {
                $validationArray[$fieldName] = rtrim($validationString, '|');
            }
        }

        // Return.
        return $validationArray;
    }

    /**
     * premiumUserCheck
     * Returns whether or not the settings make the widget
     * a premium feature.
     * --------------------------------------------------
     * @return int 1: user is premium, -1 fails, 0 default
     * --------------------------------------------------
     */
     public function premiumUserCheck() {       
        /* Premium users can see everything. */
        if ($this->user()->subscription->getSubscriptionInfo()['PE']) {
            return 1;
        } else {
            return 0;
        }
     }

    /**
     * saveSettings
     * Transforming settings to JSON format. (validation done by view)
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) {
        $settings = array();
        $oldSettings = $this->getSettings();

        // Iterating through the positions.
        foreach (array_keys($this->getSettingsFields()) as $fieldName) {
            // inputSettings. oldSettings, empty string.
            if (isset($inputSettings[$fieldName])) {
                $settings[$fieldName] = $inputSettings[$fieldName];
            } else if (isset($oldSettings[$fieldName])) {
                // Value not set, Getting from old settings.
                $settings[$fieldName] = $oldSettings[$fieldName];
            } else if (isset($this->getSettingsFields()[$fieldName]['default'])) {
                // Value not set, default found.
                $settings[$fieldName] = $this->getSettingsFields()[$fieldName]['default'];
            } else {
                $settings[$fieldName] = "";
            }
        }

        $this->settings = json_encode($settings);

        if ($commit) {
            $this->save(array('skip_settings' => TRUE));
        }
        
        /* Returning the changed fields. */ 
        $changedFields = array();
        foreach (array_diff($settings, $oldSettings) as $key=>$value) {
            if ($value) {
                array_push($changedFields, $key);
            }
        }
        return $changedFields;

    }

    /**
     * hasValidCriteria
     * Checking if the widget has valid criteria
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
    */
    public function hasValidCriteria() {
        $criteriaFields = static::getCriteriaFields();
        if (empty($criteriaFields)) {
            return TRUE;
        }
        $criteria = $this->getCriteria();
        foreach ($criteriaFields as $setting) {
            if ( ! array_key_exists($setting, $criteria) || $criteria[$setting] == '')
                return FALSE;
        }
        return TRUE;
    }


    /* -- Eloquent overridden methods -- */
    /**
     * Overriding save to add descriptor automatically.
     *
     * @return the saved object.
     * @throws DescriptorDoesNotExist
    */
    public function save(array $options=array()) {
        /* Notify user about the change */
        $this->user()->updateDashboardCache();

        if (is_null($this->descriptor_id)) {
            /* Associating descriptor. */
            $widgetDescriptor = WidgetDescriptor::where('type', $this->getType())->first();

            /* Checking descriptor. */
            if ($widgetDescriptor === null) {
                throw new DescriptorDoesNotExist("The descriptor for " . get_class($this) . " does not exist", 1);
            }

            // Assigning descriptor.
            $this->descriptor()->associate($widgetDescriptor);
        }

        // Saving settings by option.
        if ( ! array_key_exists('skip_settings', $options) || $options['skip_settings'] == FALSE) {
            $commit = FALSE;
            if (is_null($this->settings)) {
                $commit = TRUE;
            }
            $this->saveSettings(array(), $commit);
        }

        /* Saving settings.
         * Please note, that the save won't hit the db,
         * if no change has been made to the model.
        */
        return parent::save();
    }

    /**
     * newFromBuilder
     * Override the base Model function to use polymorphism.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    public function newFromBuilder($attributes=array()) {
        $className = WidgetDescriptor::find($attributes->descriptor_id)
            ->getClassName();
        $instance = new $className;
        $instance->exists = TRUE;
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;
    }

    /**
     * getErrorMessage
     * Returning the corresponding error.
     * --------------------------------------------------
     * @return string 
     * --------------------------------------------------
     */
    public function getErrorMessage() {
        $state = $this->state;
        if (strpos('error_', $state) === 0) {
            $errorCodes = $this->getErrorCodes();
            $key = substr($state, strpos($state, '_') + 1);
            
            if (array_key_exists($key, $errorCodes)) {
                return $errorCodes[$key];
            }
        }
        return '';
    }

    /**
     * checkSettingsIntegrity
     * Checking the Settings integrity of widgets.
    */
    protected function checkSettingsIntegrity() {
        $settings = $this->getSettings();
        if (is_null($settings)) {
            throw new WidgetException;
        } else if ( ! $this->hasValidCriteria()) {
            throw new WidgetFatalException;
        }
        foreach ($this->getSettingsFields() as $key=>$meta) {
            if ( ! array_key_exists($key, $settings)) {
                throw new WidgetException;
            }
        }
    }

    /**
     * getType
     * Returning the underscored type of the widget.
     * Only in generalwidget, where the descriptor is
     * still unknown.
     * --------------------------------------------------
     * (CamelCase to underscore)
     * @return string
     * --------------------------------------------------
    */
    private function getType() {
        $lowercase = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', get_class($this))), '_');
        /* Removing _widget */
        return str_replace('_widget', '', $lowercase);
    }

    /**
     * Overriding delete to update the user's cache.
    */
    public function delete() {
        /* Notify user about the change */
        $this->user()->updateDashboardCache();
        parent::delete();
    }
}

?>
