<h2 class="text-white text-center">
  <span>
    @if ($widget->state == 'missing_data')
      Data not present yet!
    @else
      @if ($widget->getSettings()['histogram'])
        @foreach ($widget->getHistogram() as $histogramEntry)
          ${{$histogramEntry}},
        @endforeach
      @else
        ${{$widget->getLatestData()}}
      @endif
    @endif
  </span>
</h2>

