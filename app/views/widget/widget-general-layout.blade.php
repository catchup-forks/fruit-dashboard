<div data-id='{{ $widget['id'] }}'
     data-row="{{ $widget['position']->row }}"
     data-col="{{ $widget['position']->col }}"
     data-sizex="{{ $widget['position']->size_x }}"
     data-sizey="{{ $widget['position']->size_y }}"
     data-min-sizex="{{ $widget['instance']->getMinCols() }}"
     data-min-sizey="{{ $widget['instance']->getMinRows() }}"
     class="gridster-widget can-hover overflow-hidden">

     @if ($widget['instance'] instanceof SharedWidget)
      <div class="position-tr-sm-second">
        <span class="fa fa-share-alt text-white display-hovered drop-shadow" data-toggle="tooltip" title="This widget is shared with you" data-placement="left"></span>
      </div> <!-- /.position-tr-sm-second -->
     @endif

    <div class="dropdown position-tr-sm">
      <a id="{{ $widget['id'] }}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="fa fa-bars drop-shadow text-white color-hovered display-hovered"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="{{ $widget['id'] }}">

        @if ($widget['state'] != 'setup_required')
        {{-- EDIT --}}
        <li>
          <a href="{{ route('widget.edit', $widget['id']) }}">
            <span class="fa fa-cog"> </span>
            Edit Settings
          </a>
        </li>

        {{-- REFRESH --}}
        @if ($widget['instance'] instanceof iAjaxWidget)
          <li>
            <a href="#" id="widget-refresh-{{$widget['id']}}" title="refresh widget content">
              <span class="fa fa-refresh"> </span>
              Refresh data
            </a>
          </li>
        @endif

        {{-- SHARE --}}
        @if ( ! $widget['instance'] instanceof SharedWidget && ! $widget['instance'] instanceof PromoWidget)
        <li>
          <a href="#" id="share-{{$widget['id']}}" onclick="showShareModal({{$widget['id']}})">
            <span class="fa fa-share-alt"> </span>
            Share widget
          </a>
        </li>
        @endif
        @endif

        {{-- DELETE --}}
        <li>
          <a href="#" class="widget-delete" data-id='{{ $widget['id'] }}' >
            <span class="fa fa-times"> </span>
            Delete widget
          </a>
        </li>

      </ul>
    </div>

  @if ($widget['instance']->premiumUserCheck() === 0)
     @include('widget.widget-premium-not-allowed', ['feature' => 'hello'])
  @elseif ($widget['state'] == 'setup_required')
      @include('widget.widget-setup-required')
  @elseif ($widget['state'] == 'rendering_error')
      @include('widget.widget-rendering-error')
  @elseif ($widget['instance'] instanceof SharedWidget)
      <div class="@if ($widget['instance']->getRelatedWidget()->state == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget['instance']->getRelatedWidget()->id}}">
    @include(
      $widget['instance']->getRelatedWidget()->getDescriptor()->getTemplateName(),
      ['widget' => $widget['instance']->getRelatedWidget()->getTemplateData()]
    )
    </div>
  @else
    @if ($widget['instance'] instanceof iAjaxWidget)
      @include('widget.widget-loading')
      <div class="@if ($widget['state'] == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget['id']}}">
    @endif
    @if ($widget['state'] != 'loading')
      @include($widget['descriptor']->getTemplateName())
    @endif
    <!-- Adding loading on DataWidget -->
    @if ($widget['instance'] instanceof iAjaxWidget)
      </div>
    @endif

  @endif
</div> <!-- /.gridster-widget -->
