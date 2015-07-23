
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stripe testing Page</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
{{ HTML::script('js/jquery.js'); }}

</head>
<body>
    <!-- Page Content -->
    <div class="container">

    <h2>Users</h2>

    <table class="col-sm-12">
    <thead style="border-bottom: 1px solid black">
        <td>Name</td>
        <td>Email</td>
        <td>Birth date</td>
        <td>Language</td>
        <td>Location</td>
        <td>Gender</td>
    </thead>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->birthday }}</td>
            <td>{{ $user->language }}</td>
            <td>{{ $user->zoneinfo }}</td>
            <td>{{ $user->gender }}</td>
        </tr>
    @endforeach
    </table>
    </div>
    <!-- /.container -->
    </body>
</html>
