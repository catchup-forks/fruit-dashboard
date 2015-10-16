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
      {{ $value }} statistics
    </h3>
  </div> <!-- /.panel-heading -->
  <div class="panel-body no-padding" id="chart-{{ $resolution }}">
    <div id="chart-container">
      <canvas class="img-responsive canvas-auto"></canvas>
    </div>
  </div> <!-- /.panel-body -->

</div> <!-- /.panel -->

<div class="panel fill panel-default panel-transparent">
  <div class="panel-heading">
    <h3 class="panel-title">{{ $value }} data history</h3>
  </div> <!-- /.panel-heading -->

  <div class="panel-body">
    <div class="row">
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            @include('singlestat.singlestat-diff',
              [ 'format'     => $widget->getFormat(),
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
            [ 'format'     => $widget->getFormat(),
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
            [ 'format'     => $widget->getFormat(),
              'values'     => $values,
              'resolution' => $resolution,
              'distance'   => 1 ] )
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            <h3 class="text-primary">{{ Utilities::formatNumber(array_values($widget->getLatestValues())[0], $widget->getFormat()) }}</h3>
            <div class="text-success">
              <span class="fa fa-check"> </span>
            </div> <!-- /.text-success -->
            <p><small>Current value</small></p>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
    </div> <!-- /.row -->
  </div> <!-- /.panel-body -->
</div> <!-- /.panel -->

<div class="panel fill panel-default panel-transparent">
  <div class="panel-heading">
    <h3 class="panel-title">{{ $value }} datatable</h3>
  </div> <!-- /.panel-heading -->

  <div class="panel-body">
    <div class="row">
      <div class="col-sm-12 table-responsive">
        <table class="table datatable">
          <thead>
            <tr>
              <th class="col-sm-1">#</th>
              @foreach($widget->getData([ 'resolution' => $resolution ])['labels'] as $datetime)
                <th>{{ $datetime }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($widget->getData([ 'resolution' => $resolution ])['datasets'] as $dataset)
              <tr>
                <td class="col-sm-1" style="background-color: rgb({{ $dataset['color'] }})"></td>
                @foreach ($dataset['values'] as $value)
                  <td>{{ $value }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div> <!-- /.col-sm-12 -->
    </div> <!-- /.row -->
  </div> <!-- /.panel-body -->
</div> <!-- /.panel -->