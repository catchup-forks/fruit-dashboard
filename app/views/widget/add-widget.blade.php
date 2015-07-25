@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="vertical-center">
    <div class="container panel">
      <table>
        <thead>
          <th>Name</th>
          <th>Description</th>
           <th>Is premium?</th>
           <th>Add</th>
        </thead>
        <tbody>
          @foreach($widgetDescriptors as $descriptor)
          <tr>
            <td>{{ $descriptor->name }}</td>
            <td>{{ $descriptor->description }}</td>
            <td>{{ $descriptor->is_premium }}</td>
            <td><a href="{{route('widget.doAdd', array($descriptor->id) )}}" class="btn btn-primary btn-flat"><span class="fa fa-plus"></span></a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->

  @stop
