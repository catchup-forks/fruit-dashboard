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
        $widget = Widget::find($widgetID)->getSpecific();

        // Widget not found.
        if ($widget === null) {
            throw new WidgetDoesNotExist("Widget not found", 1);
        }
        return $widget;
    }

    /* -- Edit settings -- */
    public function getEditWidgetSettings($widgetID) {
        // Getting the editable widget.
        try {
            $widget = $this->getWidget($widgetID);
        } catch (WidgetDoesNotExist $e) {
            return Redirect::route('dashboard.dashboard')
                ->with('error', $e->getMessage());
        }

        // Rendering view.
        return View::make('widgets.widget-settings')
            ->with('widget', $widget);
    }

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
                $widget->getSettingsValidationArray()
            )
        );

        if ($validator->fails()) {
            // Redirect.
            return Redirect::route('widget.edit-settings', array($widgetID))
                ->with('error', "Please correct the form errors.");
        }

        // Validation successful ready to save.
        $widget->saveSettings(Input::all());

        return Redirect::route('widget.edit-settings', array($widgetID))
            ->with('success', "Widget successfully updated.");
   }

    /* -- AJAX functions -- */

    /**
     * Save widget position.
     *
     * @param  int  $userId
     * @return Response
     */
    public function saveWidgetPosition($userId) {
        // Escaping invalid data.
        if (!isset($_POST['position'])) {
            throw new BadPosition("Missing data.", 1);
        }

        if (User::find($userId)) {
            $widgetData = json_decode($_POST['position'], TRUE)[0];

            // Escaping invalid data.
            if (!isset($widgetData['id'])) {
                return Response::json(array('error' => 'Invalid JSON string.'));
            }

            $widget = Widget::find($widgetData['id']);

            if ($widget === null) {
                return Response::json(array('error' => 'Invalid JSON string.'));
            }
            try{
                $widget->setPosition($widgetData);
            } catch (BadPosition $e) {
                return Response::json(array('error' => $e->getMessage()));
            }
            return Response::make('everything okay', 200);

        } else {
            // no such user
            return Response::json(array('error' => 'no such user'));
        }
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
