<div data-id='{{ $widget->id }}'
     data-row="{{ $widget->getPosition()->row }}"
     data-col="{{ $widget->getPosition()->col }}"
     data-sizex="{{ $widget->getPosition()->size_x }}"
     data-sizey="{{ $widget->getPosition()->size_y }}"
     data-min-sizex="{{ $widget->getMinCols() }}"
     data-min-sizey="{{ $widget->getMinRows() }}"
     class="gridster-player can-hover">

    <div class="dropdown position-tr-sm">
      <a id="{{ $widget->id }}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="fa fa-bars drop-shadow text-white color-hovered display-hovered"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="{{ $widget->id }}">

        {{-- EDIT --}}
        <li>
          <a href="{{ route('widget.edit', $widget->id) }}">
            <span class="fa fa-cog"> </span>
            Edit Settings
          </a>
        </li>

        {{-- REFRESH --}}
        @if ($widget instanceof iAjaxWidget)
          <li>
            <a href="#" id="refresh-{{$widget->id}}" title="refresh widget content">
              <span class="fa fa-refresh"> </span>
              Refresh data
            </a>
          </li>
        @endif

        {{-- SHARE --}}
        @if ( ! $widget instanceof SharedWidget)
        <li>
          <a href="#" id="share-{{$widget->id}}" onclick="showShareModal({{$widget->id}})">
            <span class="fa fa-share-alt"> </span>
            Share widget
          </a>
        </li>
        @endif

        {{-- DELETE --}}
        <li>
          <a class='deleteWidget' data-id='{{ $widget->id }}' href="#">
            <span class="fa fa-times"> </span>
            Delete widget
          </a>
        </li>

      </ul>
    </div>

  <!-- Adding loading on DataWidget -->
  @if ($widget->state == 'setup_required')
      @include('widget.widget-setup-required', ['widget' => $widget,])
  @elseif ($widget instanceof SharedWidget)
    @include($widget->getRelatedWidget()->descriptor->getTemplateName(), ['widget' => $widget->getRelatedWidget()])
  {{-- @elseif ($widget->premiumUserCheck() === -1) --}}
    {{-- @include('widget.widget-premium-not-allowed', ['feature' => 'hello']) --}}
  @else
    @if ($widget instanceof iAjaxWidget)
      @include('widget.widget-loading', ['widget' => $widget,])
      <div class="@if ($widget->state == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget->id}}">
    @endif

    @include($widget->descriptor->getTemplateName(), ['widget' => $widget])
    <!-- Adding loading on DataWidget -->
    @if ($widget instanceof CronWidget)
      </div>
    @endif

  @endif
</div> <!-- /.gridster-player -->