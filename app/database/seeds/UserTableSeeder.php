<?php

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();
        User::create(array(
            'id'       => '1',
            'email'    => 'rise.hun@gmail.com',
            'password' => Hash::make('supersecret'),
            'stripe_key'=> 'sk_test_YOhLG7AgROpHWUyr62TlGXmg',
        ));
        User::create(array(
            'id'       => '2',
            'email'    => 'borzos6@gmail.com',
            'password' => Hash::make('1234'),
        ));
    }

}