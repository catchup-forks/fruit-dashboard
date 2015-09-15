<?php

class FacebookLikesCountWidget extends CountWidget
{
    protected static $histogramDescriptor = 'facebook_likes';
    /* -- Settings -- */
    public static $settingsFields = array(
        'period' => array(
            'name'       => 'Period',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days'
        ),
        'multiplier' => array(
            'name'       => 'Number of periods',
            'type'       => 'INT',
            'validation' => 'required',
            'default'    => '1'
        ),
        'page' => array(
            'name'       => 'Page',
            'type'       => 'SCHOICE',
            'validation' => 'required'
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

}
?>