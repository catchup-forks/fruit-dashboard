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
                    array_keys($widget->getSetupFields())
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
            /* Everything OK, return response with 200 status code */
            return Response::make('Widget successfully deleted.', 200);

        /* GET or POST */
        } else {
            /* Redirect to dashboard */
            return Redirect::route('dashboard.dashboard')
                ->with('success', "You successfully deleted the widget");
        }
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
        // Getting the descriptor.
        $descriptor = WidgetDescriptor::find($descriptorID);
        if (is_null($descriptor)) {
            return Redirect::back()
                ->with('error', 'Descriptor not found');
        }

        // Creating new widget instance.
        $className = $descriptor->getClassName();
        $widget = new $className(array(
            'settings' => json_encode(array()),
            'state'    => 'active',
            'position' => '{"size_x": 2, "size_y": 2, "row": 0, "col": 0}',
        ));
        $widget->dashboard()->associate(Auth::user()->dashboards[0]);
        $widget->descriptor()->associate($descriptor);
        $widget->save();

        // If not setup settings redirect to dashboard automatically..
        if (empty($widget->getSetupFields())) {
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
                try{
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
     * Save widget text.
     *
     * @param  int  $widgetId
     * @param  string $text
     * @return Response
     */

    public function saveWidgetText($widgetId, $text = '')
    {
        $widgetData = Data::where('widget_id', $widgetId)->first();

        if ($widgetData)
        {
            $widgetData->data_object = $text;
            $widgetData->save();

            return Response::make('everything okay',200);
        } else {
            return Response::json(array('error' => 'bad widget id'));
        }
    }

    /**
     * Save widget name.
     *
     * @param  int  $widgetId
     * @param  string $newName
     * @return Response
     */

    public function saveWidgetName($widgetId, $newName)
    {
        $widget = Widget::find($widgetId);

        if ($widget)
        {
            $widget->widget_name = $newName;
            $widget->save();

            return Response::make('everything okay',200);
        } else {
            return Response::json(array('error' => 'bad widget id'));
        }
    }


    /**
     * Save user name.
     *
     * @param  int  $widgetId
     * @param  string $newName
     * @return Response
     */

    public function saveUserName($newName)
    {
        // selecting logged in user
        $user = Auth::user();

        $user->name = $newName;

        $user->save();

        return Response::make('everything okay',200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $dashboard = Dashboard::where('user_id','=',$id);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
} /* GeneralWidgetController */
