<?php

/**
* --------------------------------------------------------------------------
* GoogleDataCollector:
*       Getting data from google account.
* --------------------------------------------------------------------------
*/

class GoogleDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $client;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new GoogleConnector($user);
        $connector->connect();
        $this->client = $connector->getClient();
    }

    public function getData() {
        $drive_service = new Google_Service_Drive($this->client);
        $files_list = $drive_service->files->listFiles(array())->getItems();
        Log::info($files_list);
    }


} /* GoogleDataColector */
