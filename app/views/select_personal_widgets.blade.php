@if ( $errors->count() > 0 )
      <p>The following errors have occurred:</p>

      <ul>
        @foreach( $errors->all() as $message )
          <li>{{ $message }}</li>
        @endforeach
      </ul>
    @endif

<h1>Personal widgets</h1>
<table>
    <thead>
        <th>Name</th>
        <th>Descripton</th>
        <th>Is premium?</th>
    </thead>
    <tbody>
    @foreach($personalWidgets as $widget)
    <tr>
        <td>{{ $widget->name }}</td>
        <td>{{ $widget->description }}</td>
        <td>{{ $widget->is_premium }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
