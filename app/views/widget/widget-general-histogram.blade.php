{{-- here comes the wrapper for the layout chooser --}}

<div class="layout-chooser display-hovered">
  <div class="element"><i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element"><i class="fa fa-table fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element"><i class="fa fa-database fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element"><i class="fa fa-balance-scale fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
</div> <!-- /.layout-chooser -->





@if ($widget['layout'] == 'table')
  @include('widget.widget-general-table')
@elseif ($widget['layout'] == 'count')
  @include('widget.widget-general-count')
@else
  @include('widget.widget-general-chart')
@endif
