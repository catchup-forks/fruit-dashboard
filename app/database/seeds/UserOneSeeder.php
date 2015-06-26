<?php

class UserOneSeeder extends Seeder
{

    public function run()
    {
       	if (User::find(1)){ 
        	$user = User::find(1);
        }
        else {
	        User::create(array(
	            'id'       => '1',
	            'email'    => 'demo@demo.demo',
	            'password' => Hash::make('1234'),
	        ));
	        $user = User::find(1);
    	}
    	// notConnected, refactor needs to find out if this needs to be used
    	// search for ->ready
    	// $user->ready = 'notConnected';
    	
    	// dashboard for user
        $dashboard = new Dashboard;
        $dashboard->dashboard_name = "Dashboard #1";
        $dashboard->save();

        // delete existing dashboard
        $user->dashboards()->delete();
        // attach dashboard & user
        $user->dashboards()->attach($dashboard->id, array('role' => 'owner'));

        // create default widgets

        // clock widget
        $widget = new Widget;
        $widget->widget_name = 'clock widget';
        $widget->widget_type = 'clock';
        $widget->widget_source = '{}';
        $widget->position = '{"size_x":8,"size_y":6,"col":1,"row":1}';
        $widget->dashboard_id = $user->dashboards()->first()->id;
        $widget->save();

        // greeting widget
        $widget = new Widget;
        $widget->widget_name = 'greeting widget';
        $widget->widget_type = 'greeting';
        $widget->widget_source = '{}';
        $widget->position = '{"size_x":8,"size_y":6,"col":1,"row":3}';
        $widget->dashboard_id = $user->dashboards()->first()->id;
        $widget->save();

    }
}
