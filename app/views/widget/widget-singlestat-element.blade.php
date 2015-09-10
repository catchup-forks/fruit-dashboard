<div class="col-md-6 chart-container">
  <div class="panel fill panel-default panel-transparent">
    <div class="panel-heading">
      <div class="panel-title">
        @if (($widget->getSettings()['frequency'] == $frequency) && ($widget->state != 'hidden'))
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
        <a href="{{ route('widget.pin-to-dashboard', array($widget->id, $frequency)) }}"
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
      </div>
    </div>
    
    <div class="panel-body no-padding" id="chart-container-{{$frequency}}">
      <canvas id="chart-{{$frequency}}"></canvas>
    </div> <!-- /.panel-body -->
    
  </div> <!-- /.panel -->
</div> <!-- /.col-md-6 -->