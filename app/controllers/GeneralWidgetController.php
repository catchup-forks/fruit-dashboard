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
        /* Getting the editable widget. */
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }

        if ($widget->state == 'setup_required') {
            return Redirect::route('widget.reset', $widget->id);
        }

        /* Creating selectable dashboards */
        $dashboards = array();
        foreach (Auth::user()->dashboards as $dashboard) {
            $dashboards[$dashboard->id] = $dashboard->name;
        }

        /* Rendering view. */
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
            array_keys($widget->getSetupFields()),
            Input::all()
        );
        $validatorArray['dashboard'] = 'required|in:' . implode(',', $dashboardIds);

        /* Adding update_period on DataWidget */
        if ($widget instanceof DataWidget) {
            $validatorArray['update_period'] = 'required|integer|min:23';
        }

        /* Validate inputs */
        $validator = forward_static_call_array(
            array('Validator', 'make'),
            array(
                Input::all(),
                $validatorArray
            )
        );;

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
        $widget->saveSettings(Input::all());

        /* Adding update_period on DataWidget */
        if ($widget instanceof DataWidget) {
            $widget->setUpdatePeriod(Input::get('update_period'));
        }

        /* Track event | EDIT WIDGET */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Edit widget',
            'el' => $widget->getDescriptor()->getClassName())
        );

        /* Return */
        return Redirect::route('dashboard.dashboard', array('active' => $newDashboard->id))
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
                    $widget->getSetupFields(),
                    Input::all()
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

        return Redirect::route('dashboard.dashboard', array('active' => $widget->dashboard->id))
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
            $className = $widget->getDescriptor()->getClassName();

            /* Deleting old widget. */
            $widget->delete();

            /* Saving new Widget */
            $newWidget = new $className(array(
                'position' => $position,
                'state'    => 'active'
            ));
            $newWidget->dashboard()->associate($dashboard);
            $newWidget->save();

            $setupFields = $newWidget->getSetupFields();
            if ( ! empty($setupFields)) {
                return Redirect::route('widget.setup', array($newWidget->id))
                    ->with('success', 'You successfully restored the widget.');
            }
            return Redirect::route('dashboard.dashboard', array('active' => $dashboard->id))
                ->with('success', 'You successfully restored the widget.');
        }
        return Redirect::route('dashboard.dashboard')
            ->with('error', 'Something went wrong, we couldn\'t restore your widget.');
    }

    /**
     * getAddWidget
     * --------------------------------------------------
     * @return Renders the add widget page
     * --------------------------------------------------
     */
    public function getAddWidget() {
        /* Check the default dashboard and create if not exists */
        Auth::user()->checkOrCreateDefaultDashboard();

        foreach(Auth::user()->widgetSharings()->where('state', 'not_seen')->get() as $sharing) {
            $sharing->setState('seen');
        }

        /* Rendering view */
        return View::make('widget.add-widget');
    }

    /**
     * getAddWidgetWithData
     * --------------------------------------------------
     * @return Renders the add widget page
     * --------------------------------------------------
     */
    public function getAddWidgetWithData($descriptorId, $dashboardId) {
        $descriptor = WidgetDescriptor::find($descriptorId);
        $dashboard = Dashboard::find($dashboardId);

        try {
            $widget = $this->addWidget($descriptor, $dashboard);
        } catch (Exception $e) {
            return Redirect::route('widget.add')->with('error', 'Something went wrong, please try again.');
        }
        /* If widget has no setup fields, redirect to dashboard automatically */
        if ($widget->getSetupFields() == FALSE) {
            return Redirect::route('dashboard.dashboard', array('active' => $dashboard->id))
                ->with('success', 'Widget successfully created.'); }
        return Redirect::route('widget.setup', array($widget->id))
            ->with('success', 'Widget successfully created. You can customize it here.');
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

        /* Get the dashbhoard */
        $dashboardNum = Input::get('toDashboard');

        if ($dashboardNum == 0) {
            /* Add to new dashboard */

            /* Get the new name or fallback to the default */
            $newDashboardName = Input::get('newDashboardName');
            if (empty($newDashboardName)) {
                $newDashboardName = 'New Dashboard';
            }
            /* Create new dashboard and associate */
            $dashboard = new Dashboard(array(
                'name'       => $newDashboardName,
                'background' => TRUE,
                'number'     => Auth::user()->dashboards->max('number') + 1
            ));
            $dashboard->user()->associate(Auth::user());
            $dashboard->save();
        } else if ($dashboardNum > 0) {
            /* Adding to existing dashboard */
            $dashboard = Dashboard::find($dashboardNum);
            if (is_null($dashboard)) {
                $dashboard = Auth::user()->dashboards[0];
            }
        } else {
            /* Error handling */
            $dashboard = Auth::user()->dashboards[0];
        }

        try {
            $widget = $this->addWidget($descriptor, $dashboard);
        } catch (DescriptorDoesNotExist $e) {
            return Redirect::back()
                ->with('error', 'Something went wrong, your widget cannot be found.');
        } catch (ServiceException $e) {
            /* Service not connected. */
            $redirectRoute = route('service.' . $descriptor->category . '.connect') . '?descriptor=' . $descriptorID . '&dashboard=' . Input::get('toDashboard');
            return Redirect::to($redirectRoute);
        }

        /* If widget has no setup fields, redirect to dashboard automatically */
        if ($widget->getSetupFields() == FALSE) {
            return Redirect::route('dashboard.dashboard', array('active' => $dashboard->id))
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
    public function anyPinToDashboard($widgetID, $resolution) {
        /* Getting the editable widget. */
        try {
            $widget = $this->getWidget($widgetID);
            if ( ! $widget instanceof DataWidget) {
                throw new WidgetDoesNotExist("This widget does not support histograms", 1);
            }
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }
        $widget->state = 'active';
        $widget->saveSettings(array('resolution' => $resolution), TRUE);

        /* Rendering view. */
        return Redirect::route('dashboard.dashboard', array('active' => $widget->dashboard->id))
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
            }
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }

        /* Calculating values for rendering. */
        $values = array();
        foreach (SiteConstants::getSingleStatHistoryDiffs() as $resolution=>$multipliers) {
            $values[$resolution] = array();
            foreach ($multipliers as $multiplier) {
                $values[$resolution][$multiplier] = $widget->getHistory($multiplier, $resolution);
            }
        }
        /* Rendering view. */
        return View::make('singlestat.singlestat')
            ->with('widget', $widget)
            ->with('values', $values);
    }

    /**
     * anyAddNotify
     * --------------------------------------------------
     * @param (integer) ($widgetId) The ID of the widget
     * @return
     * --------------------------------------------------
     */
    public function anyAddNotify($sharingId) {
        try {
            $widget = $this->getWidget($widgetID);
            if ( ! $widget instanceof HistogramWidget) {
                throw new WidgetDoesNotExist("This widget does not support notifications yet.", 1);
            }
        } catch (WidgetDoesNotExist $e) {
            return Response::make($e->getMessage(), 401);
        }

        /* Everything OK, return response with 200 status code */
        return Response::make("Added to notifications", 200);
    }

    /**
     * anyAcceptShare
     * --------------------------------------------------
     * @param (integer) ($sharingId) The ID of the sharing
     * @return
     * --------------------------------------------------
     */
    public function anyAcceptShare($sharingId) {
        $sharing = $this->getWidgetSharing($sharingId);
        if ( ! is_null($sharing)) {
            $sharing->accept(Auth::user()->dashboards()->first()->id);
        }
        if (count(Auth::user()->getPendingWidgetSharings()) == 0) {
            return Redirect::route('dashboard.dashboard');
        }
        /* Everything OK, return response with 200 status code */
        return Redirect::route('widget.add');
    }

    /**
     * anyRejectShare
     * --------------------------------------------------
     * @param (integer) ($sharingId) The ID of the sharing
     * @return
     * --------------------------------------------------
     */
    public function anyRejectShare($sharingId) {
        $sharing = $this->getWidgetSharing($sharingId);
        if ( ! is_null($sharing)) {
            $sharing->reject();
        }

        if (count(Auth::user()->getPendingWidgetSharings()) == 0) {
            return Redirect::route('dashboard.dashboard');
        }

        /* Everything OK, return response with 200 status code */
        return Redirect::route('widget.add');
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

        /* User cross check. */
        if ($widget->user() != Auth::user() &&
                ! Auth::user()->widgetSharings()
                    ->where('widget_id', $widgetID)
                    ->first()
                ) {
            throw new WidgetDoesNotExist("You do not own this widget, nor is it shared with you.", 1);
        }

        // Widget not found.
        if ($widget === null) {
            throw new WidgetDoesNotExist("Widget not found", 1);
        }
        return $widget;
    }

    /**
     * getWidgetSharing
     * --------------------------------------------------
     * A function to return the sharing from the ID.
     * @param (int) ($sharingId) The ID of the sharing
     * @returns WidgetSharing
     * --------------------------------------------------
     */
    private function getWidgetSharing($sharingId) {
        $sharing = WidgetSharing::find($sharingId);

        // Widget not found.
        if ($sharing === null) {
            return null;
        }
        return $sharing;
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
     * @return Json with status code
     * --------------------------------------------------
     */
    public function saveWidgetPosition() {

        /* Escaping invalid data. */
        if (!isset($_POST['positioning'])) {
            throw new BadPosition("Missing positioning data.", 1);
        }

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
     * getAjaxSetting
     * --------------------------------------------------
     * Returns the widget's settings in ajax.
     * @param int $widgetId
     * @param string $fieldName
     * @param mixed $value
     * --------------------------------------------------
     */
    public function getAjaxSetting($widgetId, $fieldName, $value) {
        /* Selecing the widget */
        try {
            $widget = $this->getWidget($widgetId);
        } catch (WidgetDoesNotExist $e) {
            return Response::json(array('error' => $e));
        }

        try {
            return Response::json($widget->$fieldName($value));
        } catch (Exception $e) {
            return Response::json( array('error' => $e->getMessage()));
        }
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
        if ( ! $widget instanceof iAjaxWidget) {
            return Response::json(array('error' => 'This widget does not support this function.'));
        }

        /* Calling widget specific handler */
        return Response::json($widget->handleAjax(Input::all()));
    }

    /**
     * postShareWidget
     * --------------------------------------------------
     * @param (integer) ($widgetID) The ID of the sharable widget
     * @return Creates sharing objects
     * --------------------------------------------------
     */
    public function postShareWidget($widgetID) {
        $widget = Widget::find($widgetID);
        $emails = Input::get('email_addresses');
        if (is_null($widget) || is_null($emails)) {
            return Response::make('Bad request.', 401);
        }

        /* Splitting emails. */
        $i = 0;
        foreach (array_filter(preg_split('/[,\s]+/', $emails)) as $email) {
            /* Finding registered users. */
            $user = User::where('email', $email)
                ->first();
            if (is_null($user) || $user->id == Auth::user()->id) {
                continue;
            }

            /* Creating sharing object. */
            $sharing = new WidgetSharing(array(
                'state' => 'not_seen'
            ));
            $sharing->srcUser()->associate(Auth::user());
            $sharing->user()->associate($user);
            $sharing->widget()->associate($widget);
            $sharing->save();
        }

        /* Everything OK, return response with 200 status code */
        return Response::make('Widget shared.', 200);
    }

    /**
     * anySaveWidgetToImage
     * --------------------------------------------------
     * @param (integer) ($widgetID) The ID of the widget
     * @return Saves the widget to an image, and returns it
     * --------------------------------------------------
     */
    public function anySaveWidgetToImage($widgetID) {
        $widget = $this->getWidget($widgetID);

        /* Widget not found */
        if (is_null($widget)) {
            return Response::make('Bad request.', 401);
        }

        if ($widget->state == 'loading' || $widget->state == 'setup_required') {
            /* Widget is loading, no data is available yet. */
            $templateData = Widget::getDefaultTemplateData($widget);
        } else {
            try {
                $templateData = $widget->getTemplateData();
            } catch (Exception $e) {
                /* Something went wrong during data population. */
                Log::error($e->getMessage());
                $templateData = Widget::getDefaultTemplateData($widget);
                $widget->setState('setup_required');
            }
        }

        /* Build templatedata */
        $widgetData = array_merge($widget->getTemplateMeta(),$templateData);

        $htmlpath = public_path() . '/widgets/' . $widgetID . '.html';
        $pngpath = public_path() . '/widgets/' . $widgetID . '.png';

        $view = View::make('to-image.to-image-general-histogram', array('widget' => $widgetData));
        File::put($htmlpath, $view);

        if (App::environment('local')) {
            $html = File::get($htmlpath);
            /* On local maching the vagrant runs on port 8000. */
            $html = str_replace(
                'localhost:8001',
                'localhost:8000',
                $html
            );
            File::put($htmlpath, $html);
        }

        //Image::loadFile($htmlpath)->save($pngpath);
        //return $view;
        return Image::loadFile($htmlpath)->download('widget.png');
    }

    /**
     * addWidget
     * Adding a widget and returning it
     * --------------------------------------------------
     * @param WidgetDescriptor $descriptor
     * @param Dashboard $dashboard
     * --------------------------------------------------
     */
    private function addWidget($descriptor, $dashboard) {
        if (is_null($descriptor)) {
            throw new DescriptorDoesNotExist;
        }
        if (is_null($dashboard)) {
            throw new DescriptorDoesNotExist;
        }

        /* Create new widget instance */
        $className = $descriptor->getClassName();

        /* Looking for a connection */
        if (in_array($descriptor->category, SiteConstants::getServices())) {
            $connected = Connection::where('user_id', Auth::user()->id)->where('service', $descriptor->category)->first();
            if ( ! $connected) {
                throw new ServiceException;
            }
        }

        /* Create widget */
        $widget = new $className(array('state' => 'active'));

        /* Associate the widget to the dashboard */
        $widget->dashboard()->associate($dashboard);

        /* Finding position. */
        $widget->position = $dashboard->getNextAvailablePosition($descriptor->default_cols, $descriptor->default_rows);

        /* Associate descriptor and save */
        $options = array();

        $widget->save($options);

        /* Track event | ADD WIDGET */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Add widget',
            'el' => $className)
        );

        return $widget;
    }

} /* GeneralWidgetController */
