<h3 id="greeting" class="text-white text-center drop-shadow no-margin-top has-margin-vertical-sm truncate reset-line-height">
  Good <span class="greeting"></span>@if(isset(Auth::user()->name)), {{ Auth::user()->name }}@endif!
</h3>
  
  @section('widgetScripts')

   <script type="text/javascript">
     $(document).ready(function() {

      $('#greeting').hide();
      
      // fit the greeting on page load
      $('#greeting').fitText(1, {
        'minFontSize': 24
      });

      $('.greeting').html('{{ SiteConstants::getTimeOfTheDay() }}');

      // bind fittext to a resize event
      $('#greeting').bind('resize', function(e){
        $('#digitTime').fitText(1, {
          'minFontSize': 24
        });
      });

        $('#greeting').fadeIn(2000);  

    });
   </script>
   
  @append