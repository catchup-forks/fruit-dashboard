
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PayPal testing Page</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Page Content -->
    <!-- /.container -->
    <div>
      {{ Form::open(array('route' => 'paypal.createPlan', 'id' => 'signin-form_id' )) }}
      {{ Form::text('interval', null, array('placeholder' => 'interval')) }}* (`WEEK`, `DAY`, `YEAR`, `MONTH`) <br>
      {{ Form::text('interval_count', null, array('placeholder' => 'interval_count')) }} (Number)<br>
      {{ Form::text('name', null, array('placeholder' => 'name')) }}* <br>
      {{ Form::text('amount', null, array('placeholder' => 'amount')) }}* <br>
      {{ Form::text('currency', null, array('placeholder' => 'currency')) }}* <br>
      {{ Form::submit('Submit!') }}
      {{ Form::close() }}
    </div>
    <div>
      <table>
        <thead>
          <tr>
            <td>ID</td>
            <td>Name</td>
            <td>Interval</td>
            <td>Int. cnt</td>
            <td>Currency</td>
            <td>Amount</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
        @foreach ($plans as $plan)
          <tr>
            <td>{{ $plan['id'] }}</td>
            <td>{{ $plan['name'] }}</td>
            <td>{{ $plan['interval'] }}</td>
            <td>{{ $plan['interval_count'] }}</td>
            <td>{{ $plan['currency'] }}</td>
            <td>{{ $plan['amount'] }}</td>
            <td><a href="{{ URL::route('paypal.deleteplan', $plan['id']) }}"><button>Delete</button></td>
          </tr>
        @endforeach 
        </tbody>
      </table>
    </div>
    </body>
</html>
