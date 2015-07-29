<?php

class UserSeeder extends Seeder
{

    public function run()
    {
        /* Apply seeder only if no User exists (database reset) */
        if (!User::all()->count()) {

            User::create(array(
                'email'    => 'demo@demo.demo',
                'password' => Hash::make('1234')
            ));
            Settings::create(array(
                'user_id'            => '1',
                'background_enabled' => TRUE,
            ));
            
            /* Send message to console */
            error_log('UserSeeder | Successfully seeded');

        } else {
            /* Send message to console */
            error_log('UserSeeder | No modifications were made, because the DB is not empty');
        }
    }

} /* UserSeeder */
