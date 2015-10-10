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
            {{-- 5 years ago --}}
            {{-- 6 months ago --}}
            {{-- 12 weeks ago --}}
            {{-- 30 days ago --}}
            <h3>{{ Utilities::formatNumber($values['days'][30]['value'], $widget->getFormat()) }}</h3>
            <div class="@if ($values['days'][30]['success']) text-success @else text-danger @endif">
            @if ($values['days'][30]['percent'] >= 0)
              <span class="fa fa-arrow-up"> </span>
            @else
              <span class="fa fa-arrow-down"> </span>
            @endif
              {{-- compared to current value in percent --}}
              {{ Utilities::formatNumber($values['days'][30]['percent'], '%.2f%%') }}
            </div> <!-- /.text-success -->
            <p><small>30 days ago</small></p>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            {{-- 3 years ago --}}
            {{-- 3 months ago --}}
            {{-- 4 weeks ago --}}
            {{-- 7 days ago --}}
            <h3>{{ Utilities::formatNumber($values['days'][7]['value'], $widget->getFormat()) }}</h3>
            <div class="@if ($values['days'][7]['success']) text-success @else text-danger @endif">
            @if ($values['days'][7]['percent'] >= 0)
              <span class="fa fa-arrow-up"> </span>
            @else
              <span class="fa fa-arrow-down"> </span>
            @endif
              {{-- compared to current value in percent --}}
              {{ Utilities::formatNumber($values['days'][7]['percent'], '%.2f%%') }}
            </div> <!-- /.text-success -->
            <p><small>7 days ago</small></p>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-sm-3 -->
      <div class="col-sm-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            {{-- 1 year ago --}}
            {{-- 1 month ago --}}
            {{-- 1 week ago --}}
            {{-- 1 day ago --}}
            <h3>{{ Utilities::formatNumber($values['days'][1]['value'], $widget->getFormat()) }}</h3>
            <div class="@if ($values['days'][1]['success']) text-success @else text-danger @endif">
            @if ($values['days'][1]['percent'] >= 0)
              <span class="fa fa-arrow-up"> </span>
            @else
              <span class="fa fa-arrow-down"> </span>
            @endif
              {{ Utilities::formatNumber($values['days'][1]['percent'], '%.2f%%') }}
            </div> <!-- /.text-success -->
            <p><small>1 day ago</small></p>
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