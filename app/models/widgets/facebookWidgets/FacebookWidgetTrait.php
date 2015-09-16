<?php

trait FacebookWidgetTrait
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'resolution' => array(
            'name'       => 'Resolution',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily',
            'help_text'  => 'The resolution of the chart.'
        ),
        'page' => array(
            'name'       => 'Page',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'help_text'  => 'The widget uses this facebook page for data representation.'
        )
    );
    public static $setupSettings = array('page');
    public static $criteriaSettings = array('page');

    /* Choices functions */
    public function page() {
        $pages = array();
        foreach ($this->user()->facebookPages as $page) {
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
        $page = $this->user()->googleAnalyticsProperties()->where('id', $pageId);
        /* Invalid page in DB. */
        if (is_null($page)) {
            return $this->user()->facebookPages()->first();
        }
        return $page;
    }
}

?>
