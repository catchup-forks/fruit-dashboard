<div class="col-md-6">
  <div class="panel fill panel-default panel-transparent">
    <div class="panel-heading">
      <div class="panel-title">
        {{ $value }} statistics (Premium feature)
      </div>
    </div>
    
    <div class="panel-body no-padding" id="chart-container-{{$frequency}}">
        <p>This feature is available only for Premium users.</p><br>
        You can subscribe to the <a href="{{ route('payment.plans') }}" class="btn btn-primary btn-xs">Premium plan here</a> .
    </div> <!-- /.panel-body -->
    
  </div> <!-- /.panel -->
</div> <!-- /.col-md-6 -->