<?php

class WidgetSharing extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'state'
    );

    /* -- Relations -- */
    public function srcUser() { return $this->belongsTo('User', 'src_user_id'); }
    public function user() { return $this->belongsTo('User', 'user_id'); }
    public function widget() { return $this->belongsTo('Widget', 'widget_id'); }

    /**
     * setState
     * Setting a widget's state.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
    */
    public function setState($state) {
        $this->state = $state;
        $this->save();
    }

    /**
     * accept
     * Setting the state to accepted, and creating widget.
     * --------------------------------------------------
     * @param string $state
     * @param int $dashboardId
     * --------------------------------------------------
    */
    public function accept($dashboardId) {
        $this->setState('accepted');
        $this->createSharedWidget($dashboardId);
    }

    /**
     * reject
     * Setting the state to rejected.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
    */
    public function reject() {
        $this->setState('rejected');
    }

    /**
     * createSharedWidget
     * Creating the sharedWidgetInstance
     * --------------------------------------------------
     * @param string $state
     * @param $dashboardId
     * --------------------------------------------------
    */
    private function createSharedWidget($dashboardId) {
        /* Creating settings and instance. */
        $settings = array(
            'related_widget' => $this->widget->id,
            'sharing_object' => $this->id
        );
        $widget = new SharedWidget(array('state' => 'active'));

        /* Getting original descriptor, for positioning. */
        $originalDescriptor = $this->widget->getSpecific()->descriptor;

        /* Associate the widget to the dashboard */
        $dashboard = Dashboard::find($dashboardId);
        if (is_null($dashboard)) {
            $dashboard = $this->user->dashboards()->first();
        }
        $widget->dashboard()->associate($dashboard);

        /* Finding position. */
        $widget->position = $dashboard->getNextAvailablePosition(
            $originalDescriptor->default_cols,
            $originalDescriptor->default_rows
        );

        /* Saving widget */
        $widget->save();
        $widget->saveSettings($settings);
    }

}

?>