<div class="panel-transparent panel fill">
  <div class="panel-body fill" id="{{ $widget->id }}-article-container">
    @foreach ($widget->getData() as $article)
      <h5>{{ $article['title'] }}</h5>
      <p>{{ $article['text'] }}</p>
    @endforeach
  </div>
</div>

@section('widgetScripts')
@append