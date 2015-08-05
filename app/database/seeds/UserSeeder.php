<?php

class UserSeeder extends Seeder
{

    public function run()
    {
        /* Apply seeder only if no User exists (database reset) */
        if (!User::all()->count()) {

            $user = User::create(array(
                'name'     => 'Demo user',
                'email'    => 'demo@demo.demo',
                'password' => Hash::make('1234')
            ));
            
            Settings::create(array(
                'user_id'            => $user->id,
                'background_enabled' => TRUE,
            ));

            Subscription::create(array(
                'user_id'       => $user->id,
                'plan_id'       => Plan::where('name', 'Free')->first()->id,
                'status'        => 'active',
                'trial_status'  => 'possible',
                'trial_start'   => null,
            ));

            /* Send message to console */
            Log::info('UserSeeder | Successfully seeded');

        } else {
            /* Send message to console */
            Log::info('UserSeeder | No modifications were made, because the DB is not empty');
        }
    }

} /* UserSeeder */
