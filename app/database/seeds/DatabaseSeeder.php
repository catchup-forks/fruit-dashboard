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
        $this->call('DataDescriptorSeeder');

        /* CONFIG::LOCAL ONLY */
        if (App::environment('local')) {
            /* Nothing here */

        /* CONFIG::DEVELOPMENT ONLY */
        } else if (App::environment('development')) {
            /* Nothing here */
            
        /* CONFIG::STAGING ONLY */
        } else if (App::environment('staging')) {
            /* Nothing here */

        /* CONFIG::PRODUCTION ONLY */
        } else if (App::environment('production')) {
            /* Nothing here */

        }
    }

}
