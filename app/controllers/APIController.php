<?php

//md5(uniqid($your_user_login, true))
//base64_encode 
/**
 * --------------------------------------------------------------------------
 * APIController: Handles the Fruit Dashboard API
 * --------------------------------------------------------------------------
 */
class APIController extends BaseController
{
    /**
     * ================================================== *
     *                   CLASS ATTRIBUTES                 *
     * ================================================== *
     */
    private static $apiVersions = array('1.0');

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */
    
    /**
     * anySaveData
     * --------------------------------------------------
     * @return Saves the data to the database
     * --------------------------------------------------
     */
    public function anySaveData($apiVersion = null, $apiKey = null, $widgetID = null) {
        /* Check API version */
        if (!in_array($apiVersion, self::$apiVersions)) {
            return Response::json(array('error' => 'This API version is not supported.'));
        }

        /* Return */
        return Response::json(array('success' => 'Everything is OK.'));

        /* Try to get */
        
        // $authArray = json_decode(base64_decode($apiKey), true);
        // if (!isset($authArray['wid'])) {
        //     Log::info('API authArray decoding failed');
        //     return;
        // }
        // $widgetId = $authArray['wid'];

        // $inputArray = Input::all();

        // if (!isset($inputArray['data'])) {
        //     Log::info('API inputArray not valid');
        //     return;
        // }
        // $time = time();
        // foreach ($inputArray['data'] as $dataArray) {
        //     $data = new Data;
        //     $data->widget_id = $widgetId;
        //     $data->data_object = json_encode(array(
        //         'key'   => $dataArray['key'],
        //         'value' => $dataArray['value']
        //     ));
        //     $data->date = date("Y-m-d", $time);
        //     $data->timestamp = date('Y-m-d H:i:s', $time);
        //     $data->save();
        // }
    }

    /**
     * anyExample
     * --------------------------------------------------
     * @return Renders the example page
     * --------------------------------------------------
     */
    public function anyExample() {
        /* Render view */
        return View::make('api.example', 
                          array('apiVersion' => end(self::$apiVersions),
                                'apiKey'     => md5(Auth::user()->id),
                                'widgetID'   => '-WidgetID-'));
    }
}
