<?php

class UserTableSeeder extends Seeder
{

    public function run()
    {
        User::create(array(
            'id'       => '1',
            'email'    => 'demo@demo.demo',
            'password' => Hash::make('1234')
        ));
        Settings::create(array(
            'id'                 => '1',
            'user_id'            => '1',
            'background_enabled' => TRUE,
        ));
    }

}
