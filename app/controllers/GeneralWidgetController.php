<?php

/**
 * --------------------------------------------------------------------------
 * GeneralWidgetController: Handles the widget related functions
 * --------------------------------------------------------------------------
 */
class GeneralWidgetController extends BaseController {

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getEditWidgetSettings
     * --------------------------------------------------
     * @return Renders the Edit widget page (edit existing widget)
     * --------------------------------------------------
     */
    public function getEditWidgetSettings($widgetID) {
        // Getting the editable widget.
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }

        /* Creating selectable dashboards */
        $dashboards = array();
        foreach (Auth::user()->dashboards as $dashboard) {
            $dashboards[$dashboard->id] = $dashboard->name;
        }

        // Rendering view.
        return View::make('widget.edit-widget')
            ->with('widget', $widget)
            ->with('dashboards', $dashboards);
    }

    /**
     * postEditWidgetSettings
     * --------------------------------------------------
     * @return Saves the widget settings (edit existing widget)
     * --------------------------------------------------
     */
    public function postEditWidgetSettings($widgetID) {
        /* Get the editable widget */
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e);
        }

        /* Creating selectable dashboards */
        $dashboardIds = array();
        foreach (Auth::user()->dashboards as $dashboard) {
            array_push($dashboardIds, $dashboard->id);
        }

        /* Getting widget's validation array. */
        $validatorArray =  $widget->getSettingsValidationArray(
            array_keys($widget->getSetupFields())
        );
        $validatorArray['dashboard'] = 'required|in:' . implode(',', $dashboardIds);

        /* Validate inputs */
        $validator = forward_static_call_array(
            array('Validator', 'make'),
            array(
                Input::all(),
                $validatorArray
            )
        );

        /* Validation failed, go back to the page */
        if ($validator->fails()) {
            return Redirect::back()
                ->with('error', "Please correct the form errors.")
                ->withErrors($validator)
                ->withInput(Input::all());
        }

        /* Checking for dashboard change. */
        $newDashboard = Dashboard::find(Input::get('dashboard'));
        if ($widget->dashboard->id != $newDashboard->id) {
            $pos = $widget->getPosition();
            $widget->position = $newDashboard->getNextAvailablePosition($pos->size_x, $pos->size_y);
            $widget->dashboard()->associate($newDashboard);
        }

        /* Validation succeeded, ready to save */
        $widget->saveSettings(Input::except('_token'));

        /* Track event | EDIT WIDGET */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Edit widget',
            'el' => $widget->descriptor->getClassName())
        );

        /* Return */
        return Redirect::route('dashboard.dashboard')
            ->with('success', "Widget successfully updated.");
    }

    /**
     * getSetupWidget
     * --------------------------------------------------
     * @return Renders the setup widget page (add new widget)
     * --------------------------------------------------
     */
    public function getSetupWidget($widgetID) {
        // Getting the editable widget.
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }
        // Rendering view.
        return View::make('widget.setup-widget')
            ->with('widget', $widget)
            ->with('settings', array_intersect_key(
                $widget->getSettingsFields(),
                array_flip($widget->getSetupFields())
            ));
    }

    /**
     * postSetupWidget
     * --------------------------------------------------
     * @return Saves the widget settings (add new widget)
     * --------------------------------------------------
     */
    public function postSetupWidget($widgetID) {
        // Getting the editable widget.
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e);
        }
        // Validation.
        $validator = forward_static_call_array(
            array('Validator', 'make'),
            array(
                Input::all(),
                $widget->getSettingsValidationArray(
                    $widget->getSetupFields()
                )
            )
        );

        if ($validator->fails()) {
            // validation failed.
            return Redirect::back()
                ->with('error', "Please correct the form errors.")
                ->withErrors($validator)
                ->withInput(Input::all());
        }

        /* Validation successful, ready to save. */
        $widget->saveSettings(Input::all());

        return Redirect::route('dashboard.dashboard')
            ->with('success', "Widget successfully updated.");
    }

    /**
     * anyDeleteWidget
     * --------------------------------------------------
     * @param (integer) ($widgetID) The ID of the deletable widget
     * @return Deletes a widget
     * --------------------------------------------------
     */
    public function anyDeleteWidget($widgetID) {
        /* Find and remove widget */
        $widget = Widget::find($widgetID);
        if (!is_null($widget)) {
            $widget->delete();
        }

        /* USING AJAX */
        if (Request::ajax()) {
            /* Everything OK, return empty json */
            return Response::json(array());
        /* GET or POST */
        } else {
            /* Redirect to dashboard */
            return Redirect::route('dashboard.dashboard')
                ->with('success', "You successfully deleted the widget");
        }
    }

    /**
     * anyResetWidget
     * --------------------------------------------------
     * @param (integer) ($widgetID) The ID of the deletable widget
     * @return Resets a widget
     * --------------------------------------------------
     */
    public function anyResetWidget($widgetID) {
        /* Find and remove widget */
        $widget = Widget::find($widgetID);
        if (!is_null($widget)) {
            /* Saving old widget data */
            $position = $widget->position;
            $dashboard = $widget->dashboard;
            $className = $widget->descriptor->getClassName();

            /* Deleting old widget. */
            $widget->delete();

            /* Saving new Widget */
            $newWidget = new $className(array(
                'position' => $position,
                'state'    => 'active'
            ));
            $newWidget->dashboard()->associate($dashboard);
            $newWidget->save();
        }

        $setupFields = $newWidget->getSetupFields();
        if (empty($setupFields)) {
            return Redirect::route('dashboard.dashboard')
                ->with('success', 'You successfully restored the widget.'); }
        return Redirect::route('widget.setup', array($newWidget->id))
            ->with('success', 'You successfully restored the widget.');
    }
    /**
     * getAddWidget
     * --------------------------------------------------
     * @return Renders the add widget page
     * --------------------------------------------------
     */
    public function getAddWidget() {
        /* Detect if the user has no dashboard, and redirect */
        if (!Auth::user()->dashboards()->count()) {
            return Redirect::route('signup-wizard.personal-widgets');
        }

        /* Rendering view */
        return View::make('widget.add-widget')
            ->with('widgetDescriptors', WidgetDescriptor::all());
    }

    /**
     * postAddWidget
     * --------------------------------------------------
     * @return Creates a widget instance and sends to wizard.
     * --------------------------------------------------
     */
    public function postAddWidget($descriptorID) {
        /* Get the widget descriptor */

        $descriptor = WidgetDescriptor::find($descriptorID);
        if (is_null($descriptor)) {return Redirect::back()
                ->with('error', 'Something went wrong, your widget cannot be found.');
        }
        /* Create new widget instance */
        $className = $descriptor->getClassName();

        /* Looking for a connection */
        if ($descriptor->category != 'personal') {
            $connected = Connection::where('user_id', Auth::user()->id)->where('service', $descriptor->category)->first();
            if ( ! $connected) {
                $redirectRoute = 'service.' . $descriptor->category . '.connect';
                return Redirect::route($redirectRoute);
            }
        }

        /* Create widget */
        $widget = new $className(array(
            'settings' => json_encode(array()),
            'state'    => 'active',
        ));

        /* Associate to dashboard */
        if (Input::get('toDashboard')) {
            $dashboard = Dashboard::find(Input::get('toDashboard'));
            if (is_null($dashboard)) {
                $dashboard = Auth::user()->dashboards[0];
            }
        } else {
            $dashboard = Auth::user()->dashboards[0];
        }

        $widget->dashboard()->associate($dashboard);

        /* Finding position. */
        $widget->position = $dashboard->getNextAvailablePosition($descriptor->default_cols, $descriptor->default_rows);

         /* Associate descriptor and save */
        $widget->descriptor()->associate($descriptor);
        $widget->save();

        /* Start trial period if the widget is premium */
        if ($widget->descriptor->is_premium) {
            Auth::user()->subscription->changeTrialState('active');
        }

        /* Track event | ADD WIDGET */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Add widget',
            'el' => $className)
        );

        /* If widget has no setup fields, redirect to dashboard automatically */
        $setupFields = $widget->getSetupFields();
        if (empty($setupFields)) {
            return Redirect::route('dashboard.dashboard')
                ->with('success', 'Widget successfully created.'); }
        return Redirect::route('widget.setup', array($widget->id))
            ->with('success', 'Widget successfully created. You can customize it here.');
    }

    /**
     * anyPinToDashboard
     * --------------------------------------------------
     * @return Pinning a widget to dashboard.
     * --------------------------------------------------
     */
    public function anyPinToDashboard($widgetID, $frequency) {
        /* Getting the editable widget. */
        try {
            $widget = $this->getWidget($widgetID);
            if ( ! $widget instanceof DataWidget) {
                throw new WidgetDoesNotExist("This widget does not support histograms", 1);
            } } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }
        $settings = $widget->getSettings();
        $settings['frequency'] = $frequency;
        $widget->state = 'active';
        $widget->saveSettings($settings, TRUE);

        /* Rendering view. */
        return Redirect::route('dashboard.dashboard')
            ->with('success', 'Widget pinned successfully.');
    }

    /**
     * getSinglestat
     * --------------------------------------------------
     * @return Renders the singlestat page on histogram widgets.
     * --------------------------------------------------
     */
    public function getSinglestat($widgetID) {
        /* Getting the editable widget. */
        try {
            $widget = $this->getWidget($widgetID);
            if ( ! $widget instanceof DataWidget) {
                throw new WidgetDoesNotExist("This widget does not support histograms", 1);
            } } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }

        /* Rendering view. */
        return View::make('widget.histogram-singlestat')
            ->with('widget', $widget);
    }
    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * getWidget
     * --------------------------------------------------
     * A function to return the widget from the ID.
     * @param (int) ($widgetID) The ID of the widget
     * @throws WidgetDoesNotExist
     * @return mixed Response on fail, widget otherwise.
     * --------------------------------------------------
     */
    private function getWidget($widgetID) {
        $widget = Widget::find($widgetID);

        // Widget not found.
        if ($widget === null) {
            throw new WidgetDoesNotExist("Widget not found", 1);
        }
        return $widget->getSpecific();
    }

    /**
     * ================================================== *
     *                   AJAX FUNCTIONS                   *
     * ================================================== *
     */

    /**
     * saveWidgetPosition
     * --------------------------------------------------
     * Saves the widget position.
     * @param  (int)  ($userID) The ID of the user
     * @return Json with status code
     * --------------------------------------------------
     */
    public function saveWidgetPosition($userID) {

        /* Escaping invalid data. */
        if (!isset($_POST['positioning'])) {
            throw new BadPosition("Missing positioning data.", 1);
        }

        /* Find user and save positioning if possible */
        if (User::find($userID)) {
            /* Get widgets data */
            $widgets = json_decode($_POST['positioning'], TRUE);

            /* Iterate through all widgets */
            foreach ($widgets as $widgetData){

                /* Escaping invalid data. */
                if (!isset($widgetData['id'])) {
                    return Response::json(array('error' => 'Invalid JSON input.'));
                }

                /* Find widget */
                $widget = Widget::find($widgetData['id'])->getSpecific();

                /* Skip widget if not found */
                if ($widget === null) { continue; }

                /* Set position */
                try {
                    $widget->setPosition($widgetData);
                } catch (BadPosition $e) {
                    return Response::json(array('error' => $e->getMessage()));
                }
            }

        /* No user found with the requested ID */
        } else {
            return Response::json(array('error' => 'No user found with the requested ID'));
        }

        /* Everything OK, return response with 200 status code */
        return Response::make('Widget positions saved.', 200);
    }

    /**
     * getWidgetDescriptor
     * --------------------------------------------------
     * Returns the widget descriptor's data in json.
     * @param  (int)  ($descriptorID) The ID of the descriptor.
     * @return Json with descriptor data.
     * --------------------------------------------------
     */
    public function getWidgetDescriptor() {
        /* Escaping invalid data. */
        if (!Input::get('descriptorID')) {
            return Response::json(array('error' => 'Descriptor not found'));
        }

        /* Getting descriptor from DB. */
        $descriptor = WidgetDescriptor::find(Input::get('descriptorID'));

        /* Descriptor not found */
        if (is_null($descriptor)) {
            return Response::json(array('error' => 'Descriptor not found'));
        }

        /* Returning widget descriptor description. */
        return Response::json(array(
            'description' => $descriptor->description,
            'name'        => $descriptor->name,
            'type'        => $descriptor->type,
        ));
    }


    /**
     * ajaxHandler
     * --------------------------------------------------
     * Handling widget ajax functions.
     * @param  (int)  ($widgetID) The ID of the widget
     * @return Json with status code
     * --------------------------------------------------
     */
    public function ajaxHandler($widgetID) {
        /* Getting widget */
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Response::json(array('error' => $e));
        }

        /* Checking if it's an ajax widget */
        if (!$widget instanceof DataWidget) {
            return Response::json(array('error' => 'This widget does not support this function.'));
        }

        /* Calling widget specific handler */
        return Response::json($widget->handleAjax(Input::all()));
    }

} /* GeneralWidgetController */
