<?php

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* CONFIG::ALL */
        $this->call('PlanSeeder');
        $this->call('WidgetDescriptorSeeder');

        /* CONFIG::LOCAL ONLY */
        if (App::environment('local')) {
            Eloquent::unguard();
            $this->call('UserSeeder');

        /* CONFIG::DEVELOPMENT ONLY */
        } else if (App::environment('development')) {
            Eloquent::unguard();
            $this->call('UserSeeder');

        /* CONFIG::STAGING ONLY */
        } else if (App::environment('staging')) {
            /* Nothing here */

        /* CONFIG::PRODUCTION ONLY */
        } else if (App::environment('production')) {
            /* Nothing here */

        }
    }

}
