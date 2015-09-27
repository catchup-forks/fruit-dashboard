<div class="twitter-mentions" id="mentions-{{ $widget->id }}">
  {{-- for each mention --}}
  @foreach ($widget->getData() as $tweet)
  <blockquote class="twitter-tweet">
    <p>
    @foreach (explode(' ', $tweet['text']) as $word)
      @if (strlen($word) > 0 && $word[0] == '@')
        <a href="https://twitter.com/{{ ltrim($word, '@') }}">{{$word}}</a>
      @elseif (strlen($word) > 0 && $word[0] == '#')
        <a href="https://twitter.com/hashtag/{{ ltrim($word, '@') }}">{{$word}}</a>
      @elseif (strpos($word, 'http://') === 0 || strpos($word, 'https://') === 0)
        <a href="{{$word}}}}">{{$word}}</a>
      @else
        {{ $word }}
      @endif
    @endforeach
    </p>
    — {{ $tweet['name']}} ({{ $tweet['title'] }})
    <a href="https://twitter.com/Interior/status/{{ $tweet['id'] }}">{{ $tweet['created'] }}</a>
  </blockquote>
  @endforeach
  {{-- endforeach --}}
</div>
@section('widgetScripts')
<script type="text/javascript">
  // $(document).ready(function(){
  //   $("#refresh-{{$widget->id}}").click(function () {
  //     refreshWidget({{ $widget->id }}, function (data) {
  //       console.log(data);
  //       updateMentionsWidget(data, 'mentions-{{ $widget->id }}');
  //    });
  //  });
  // });
</script>
@append