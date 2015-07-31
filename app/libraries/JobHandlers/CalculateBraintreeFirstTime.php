<?php

class CalculateBraintreeFirstTime 
{
	public function fire($job,$data)
	{
		// we only get the user ID, get the user from it
		$user = User::find($data['userID']);

        BraintreeHelper::firstTime($user);

        $user->ready = 'connected';
        $user->save();
        
        Log::info('Sending "ready" email for user: '.$user->email);
        // $email = Mailman::make('emails.connected.notification')
        //     ->to($user->email)
        //     ->subject('Your metrics are ready!')
        //     ->send();


        $job->delete();
	}
}

?>