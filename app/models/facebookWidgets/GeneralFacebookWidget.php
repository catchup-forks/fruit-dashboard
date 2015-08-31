<?php

abstract class GeneralFacebookWidget extends HistogramWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'frequency' => array(
            'name'       => 'Frequency',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily'
        ),
        'page' => array(
            'name'       => 'Page',
            'type'       => 'SCHOICE',
            'validation' => 'required'
        )
    );
    public static $setupSettings = array('page');

    /* Choices functions */
    public function page() {
        $pages = array();
        foreach (Auth::user()->facebookPages as $page) {
            $pages[$page->id] = $page->name;
        }
        return $pages;
    }

    /**
     * getPage
     * --------------------------------------------------
     * Returning the corresponding page.
     * @return FacebookPage
     * @throws FacebookNotConnected
     * --------------------------------------------------
     */
    protected function getPage() {
        $pageId = $this->getSettings()['page'];
        $page = FacebookPage::find($pageId);
        /* Invalid page in DB. */
        if (is_null($page)) {
            return Auth::user()->facebookPages()->first();
        }
        return $page;
    }
}

?>
