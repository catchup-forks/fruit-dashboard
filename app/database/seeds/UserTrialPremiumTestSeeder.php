<?php

class UserTrialPremiumTestSeeder extends Seeder
{

    public function run()
    {
       
        User::create(array(
            'id'       => '2',
            'email'    => 'asdf@asdf.asdf',
            'password' => Hash::make('1234'),
            'plan' => 'trial',
            'trial_started' => Carbon::now()->subDays(31)
        ));
        UserTrialPremiumTestSeeder::addWidgetsToUser(2);

        User::create(array(
            'id'       => '3',
            'email'    => 'asd@asd.asd',
            'password' => Hash::make('1234'),
            'plan' => 'trial',
            'trial_started' => Carbon::now()->subDays(23)
        ));
        UserTrialPremiumTestSeeder::addWidgetsToUser(3);

        User::create(array(
            'id'       => '4',
            'email'    => 'as@as.as',
            'password' => Hash::make('1234'),
            'plan' => 'trial',
            'trial_started' => Carbon::now()->subDays(1)
        ));
        UserTrialPremiumTestSeeder::addWidgetsToUser(4);

        User::create(array(
            'id'       => '5',
            'email'    => 'ase@ase.ase',
            'password' => Hash::make('1234'),
            'plan' => 'trial',
            'trial_started' => Carbon::now()->subDays(26)
        ));
        UserTrialPremiumTestSeeder::addWidgetsToUser(5);

        User::create(array(
            'id'       => '6',
            'email'    => 'asef@asef.asef',
            'password' => Hash::make('1234'),
            'plan' => 'trial',
            'trial_started' => Carbon::now()->subDays(33)
        ));
        UserTrialPremiumTestSeeder::addWidgetsToUser(6);

    }

    public function addWidgetsToUser($id){
        $user = User::find($id);
        // notConnected, refactor needs to find out if this needs to be used
        // search for ->ready
        // $user->ready = 'notConnected';
        
        // dashboard for user
        $dashboard = new Dashboard;
        $dashboard->dashboard_name = "Dashboard " + $id;
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

        // save the widget
        $widgetData = array();
        $widgetJson = json_encode($widgetData);

        $widget = new Widget;
        $widget->widget_name = 'API widget';
        $widget->widget_type = 'api';
        $widget->widget_provider = 'api';
        $widget->widget_source = $widgetJson;
        $widget->dashboard_id = User::find($id)->dashboards()->first()->id;
        $widget->position = '{"size_x":3,"size_y":3,"col":1,"row":1}';
        $widget->widget_ready = false;  # widget needs data to load to show properly
        $widget->save();

    }
}
