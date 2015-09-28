<div class="text-white text-center drop-shadow quote">
  <div class="margin-top-sm has-margin-horizontal">
    <p class="lead body" id="quote-{{ $widget->id }}">
      {{ $widget->getData()['quote'] }}
    </p>
    <p class="source" id="author-{{ $widget->id }}">
      {{ $widget->getData()['author'] }}
    </p>
  </div> <!-- /.container -->
</div>

@section('widgetScripts')
@append