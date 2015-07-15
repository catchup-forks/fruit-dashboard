@if ( $errors->count() > 0 )
      <p>The following errors have occurred:</p>

      <ul>
        @foreach( $errors->all() as $message )
          <li>{{ $message }}</li>
        @endforeach
      </ul>
    @endif

<a href="{{$stripeConnectURI}}">Connect stripe.</a>
<a href="{{ URL::route('dev.stripe_load') }}">Update stripe data.</a>
<h1>Stripe plans</h1>
<table>
    <thead>
        <th>Name</th>
        <th>Interval</th>
        <th>Interval count</th>
        <th>Amount</th>
        <th>Currency</th>
    </thead>
    <tbody>
    @foreach($stripeData['plans'] as $plan)
    <tr>
        <td>{{ $plan->name }}</td>
        <td>{{ $plan->interval }}</td>
        <td>{{ $plan->interval_count }}</td>
        <td>{{ $plan->amount }}</td>
        <td>{{ $plan->currency }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<h1>Stripe subscriptions</h1>
<table>
    <thead>
        <th>Start</th>
        <th>Status</th>
        <th>Customer</th>
        <th>Quantity</th>
    </thead>
    <tbody>
    @foreach($stripeData['subscriptions'] as $subscription)
    <tr>
        <td>{{ $subscription->start }}</td>
        <td>{{ $subscription->status }}</td>
        <td>{{ $subscription->customer }}</td>
        <td>{{ $subscription->quantity }}</td>
    </tr>
    @endforeach
    </tbody>
</table>