<?php

class CalculateFirstTime 
{
	public function fire($job,$data)
	{
		// we only get the user ID, get the user from it
		$user = User::find($data['userID']);

    Calculator::calculateMetricsOnConnect($user);

    $user->ready = 'connected';
    $user->save();

    try {
            Log::info('Sending "ready" email for user: '.$user->email);
            // $email = Mailman::make('emails.connected')
            //         ->to($user->email)
            //         ->subject('Your metrics are ready!')
            //         ->send();
    } catch (Exception $e) {
            Log::info('Caught exception: '.$e->getMessage());
    }

    $job->delete();
	}
}

?>