<div class="twitter-mentions" id="mentions-{{ $widget['id'] }}">

  <div class="widget-heading larger-text">
    <i class="fa fa-twitter"> </i>
    Twitter Mentions
  </div> <!-- /.widget-heading -->

  {{-- for each mention --}}
  @foreach ($widget['data'] as $tweet)
  <blockquote class="twitter-tweet">
    <p>
    @foreach (explode(' ', $tweet['text']) as $word)
      @if (strlen($word) > 0 && strpos($word, '@') === 0)
        <a href="https://twitter.com/{{ ltrim($word, '@') }}" target="_blank">{{$word}}</a>
      @elseif (strlen($word) > 0 && strpos($word, '#') === 0)
        <a href="https://twitter.com/hashtag/{{ ltrim($word, '#') }}" target="_blank">{{$word}}</a>
      @elseif (strpos($word, 'http://') === 0 || strpos($word, 'https://') === 0)
        <a href="{{$word}}}}" target="_blank">{{$word}}</a>
      @else
        {{ $word }}
      @endif
    @endforeach
    </p>
    — {{ $tweet['name'] }} (
      <a href="https://twitter.com/{{ ltrim( $tweet['title'], '@') }}" target="_blank">{{ $tweet['title'] }}
      </a>
)
    <a href="https://twitter.com/Interior/status/{{ $tweet['id'] }}" target="_blank">{{ $tweet['created'] }}</a>
  </blockquote>
  @endforeach
  {{-- endforeach --}}
</div>
@section('widgetScripts')
<script type="text/javascript">
</script>
@append
