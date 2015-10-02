  <div class="panel fill panel-default panel-transparent">
    <div class="panel-heading">
      <div class="panel-title">
        {{ $value }} statistics (Premium feature)
      </div>
    </div>

    <div class="panel-body" id="{{ $resolution }}-chart-container">
        <p class="lead text-center">{{ $value }} statistics</p>
        <a href="{{ route('payment.plans') }}">
          <img src="/img/demonstration/graph_transparent.png" class="locked center-block">
        </a>
        <p class="text-center">
        This feature is available only for Premium users.
        <br>
        <a href="{{ route('payment.plans') }}" class="btn btn-primary btn-xs margin-top-sm">See plans &amp; pricing</a>
        </p>
    </div> <!-- /.panel-body -->

  </div> <!-- /.panel -->