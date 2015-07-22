<?php

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* CONFIG::LOCAL */
        if (App::environment('local')) {
            Eloquent::unguard();
            $this->call('UserTableSeeder');
            $this->call('InitialSeeder');

        /* CONFIG::DEVELOPMENT */
        } else if (App::environment('development')) {
            Eloquent::unguard();
            $this->call('UserTableSeeder');
            $this->call('InitialSeeder');

        /* CONFIG::STAGING */
        } else if (App::environment('staging')) {
            /* Nothing here */

        /* CONFIG::PRODUCTION */
        } else if (App::environment('production')) {
            /* Nothing here */
        
        }

        //$this->call('UserOneSeeder');
        //$this->call('UserTrialPremiumTestSeeder');
        //$this->call('UserTableExtendSeeder');
        //$this->call('supdashboarddbTableSeeder');
        //$this->call('ConnectedServicesSeeder');
        //$this->call('ExtendDefaultsSeeder');
    }

}
