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

        // Rendering view.
        return View::make('widget.edit-widget')
            ->with('widget', $widget);
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

        /* Validate inputs */
        $validator = forward_static_call_array(
            array('Validator', 'make'),
            array(
                Input::all(),
                $widget->getSettingsValidationArray(
                    array_keys($widget->getSetupFields())
                )
            )
        );

        /* Validation failed, go back to the page */
        if ($validator->fails()) {
            return Redirect::back()
                ->with('error', "Please correct the form errors.")
                ->withErrors($validator)
                ->withInput(Input::all());
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

        // Validation successful, ready to save.
        $widget->saveSettings(Input::except('_token'));

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
        $widget->delete();

        /* USING AJAX */
        if (Request::ajax()) {
            /* Everything OK, return empty json */
            return Response::json(array());
        /* GET or POST */
        } else {
            /* Redirect to dashboard */
            return Redirect::route('dashboard.dashboard')
                ->with('success', "You successfully deleted the widget"); }
    }

    /**
     * getAddWidget
     * --------------------------------------------------
     * @return Renders the add widget page
     * --------------------------------------------------
     */
    public function getAddWidget() {
        // Rendering view.
        return View::make('widget.add-widget')
            ->with('widgetDescriptors', WidgetDescriptor::all());
    }

    /**
     * doAddWidget
     * --------------------------------------------------
     * @return Creates a widget instance and sends to wizard.
     * --------------------------------------------------
     */
    public function doAddWidget($descriptorID) {

        /* Get the widget descriptor */
        $descriptor = WidgetDescriptor::find($descriptorID);
        if (is_null($descriptor)) {
            return Redirect::back()
                ->with('error', 'Something went wrong, your widget cannot be found.');
        }

        /* Create new widget instance */
        $className = $descriptor->getClassName();
        $widget = new $className(array(
            'settings' => json_encode(array()),
            'state'    => 'active',
            'position' => '{"size_x": 2, "size_y": 2, "row": 0, "col": 0}',
        ));
        $widget->dashboard()->associate(Auth::user()->dashboards[0]);
        $widget->descriptor()->associate($descriptor);
        $widget->save();

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
                ->with('success', 'Widget successfully created.');
        } else {
             return Redirect::route('widget.setup', array($widget->id))
                ->with('success', 'Widget successfully created, please set it up.');
        }
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
                $widget = Widget::find($widgetData['id']);

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
        if (!$widget instanceof iAjaxWidget) {
            return Response::json(array('error' => 'This widget does not support this function.'));
        }

        /* Calling widget specific handler */
        return Response::json($widget->handleAjax(Input::all()), 200);
    }

} /* GeneralWidgetController */
