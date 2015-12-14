<div id="panel-{{ $resolution }}" class="panel fill panel-default panel-transparent" style="height:400px">
  <div class="panel-heading">
    <h3 class="panel-title">
      @if (($widget->getSettings()['resolution'] == $resolution) && ($widget->state != 'hidden'))
      <span
       class="drop-shadow z-top pull-right"
       data-toggle="tooltip"
       data-placement="left"
       title="This chart is currently pinned to the dashboard">
       <span class="label label-success label-as-badge valign-middle">
        <span class="icon fa fa-tag">
        </span>
        </span>
      </span>
      @else
      <a href="{{ route('widget.pin-to-dashboard', array($widget->id, $resolution)) }}"
       class="drop-shadow z-top no-underline pull-right"
       data-toggle="tooltip"
       data-placement="left"
       title="Pin this chart to the dashboard">
       <span class="label label-info label-as-badge valign-middle">
         <span class="icon fa fa-thumb-tack">
         </span>
       </span>
      </a>
      @endif
      {{ Utilities::underscoreToCamelCase($value) }} statistics
    </h3>
  </div> <!-- /.panel-heading -->
  <div class="panel-body no-padding" id="widget-layout-{{ $layout }}-{{ $resolution }}">
    <div id="chart-container">
      <canvas class="img-responsive canvas-auto"></canvas>
    </div>
  </div> <!-- /.panel-body -->

</div> <!-- /.panel -->

<div class="panel fill panel-default panel-transparent">
  <div class="panel-heading">
    <h3 class="panel-title">{{ Utilities::underscoreToCamelCase($value) }} data history</h3>
  </div> <!-- /.panel-heading -->

  <div class="panel-body">
    <div class="row">
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            @include('singlestat.singlestat-diff',
              [ 'format'     => $format,
                'values'     => $values,
                'resolution' => $resolution,
                'distance'   => (($resolution=='days') ? 30 : (($resolution=='weeks') ? 12 : (($resolution=='months') ? 6 : 5))) ] )
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            @include('singlestat.singlestat-diff',
            [ 'format'     => $format,
              'values'     => $values,
              'resolution' => $resolution,
              'distance'   => (($resolution=='days') ? 7 : (($resolution=='weeks') ? 4 : (($resolution=='months') ? 3 : 3))) ] )
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            @include('singlestat.singlestat-diff',
            [ 'format'     => $format,
              'values'     => $values,
              'resolution' => $resolution,
              'distance'   => 1 ] )
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            @if(count($widget->getLatestValues())>0)
              <h3 class="text-primary">{{ Utilities::formatNumber(array_values($widget->getLatestValues())[0], $format) }}</h3>
              <div class="text-success">
                <span class="fa fa-check"> </span>
              </div> <!-- /.text-success -->
            @else
              <h3 class="text-primary">0</h3>
              <div class="text-danger">
                <span class="fa fa-ban"> </span>
              </div> <!-- /.text-danger -->
            @endif
            <p><small>Current value</small></p>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
    </div> <!-- /.row -->
  </div> <!-- /.panel-body -->
</div> <!-- /.panel -->

<div class="panel fill panel-default panel-transparent">
  <div class="panel-heading">
    <h3 class="panel-title">{{ Utilities::underscoreToCamelCase($value) }} datatable</h3>
  </div> <!-- /.panel-heading -->
  <div class="panel-body">
    <table class="table table-condensed table-bordered text-center">
      <thead>
        <th>Dataset</th>
      @foreach($chartData[$resolution]['labels'] as $label)
        <th class="text-center">{{ $label }}</th>
      @endforeach

      </thead>
      <tbody>
      @foreach($chartData[$resolution]['datasets'] as $dataset)
        <tr>
          <th> <i class="fa fa-square" style="color:rgb({{ $dataset['color'] }})"></i> {{ $dataset['name'] }} </th>
          @foreach ($dataset['values'] as $key=>$value)
          <td> {{ Utilities::formatNumber($value, $format) }}</td>
          @endforeach
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div> <!-- /.panel -->
