@extends('meta.base-user-signout')

@section('pageContent')

<p>Dummy user information:</p><br>
@foreach($user_info as $key => $value)
	<p>{{ $key }} => {{ $value }}</p>
@endforeach

<p>Dummy payment information for user:</p><br>
@foreach($payment_info as $key => $value)
	<p>{{ $key }} => {{ $value }}</p>
@endforeach

@stop
