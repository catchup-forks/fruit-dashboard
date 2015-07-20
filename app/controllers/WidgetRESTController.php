<?php

class WidgetRESTController extends BaseController {

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

        $user = User::where('id','=',$userId)->first();

        if ($user) {
            $widgetData = json_decode($_POST['position'], TRUE)[0];

            // Escaping invalid data.
            if (!isset($widgetData['id'])) {
                throw new BadPosition("Invalid json postion value: ".$_POST['position']."", 1);
            }

            $widget = Widget::find($widgetData['id']);

            if ($widget === null) {
                throw new BadPosition("Invalid widget: ".$_POST['position']."", 1);
                return;
            }

            $widget->setPosition($widgetData);
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
}
