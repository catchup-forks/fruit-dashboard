<script type="text/javascript">
function easyGrowl(type, message, durationMS) {
  var growlOptions = {
    message: message,
    size: "large",
    duration: durationMS,
    location: "br"
  };

  switch(type) {
    case 'success':
        growlOptions['title'] = 'Success!';
        $.growl.notice(growlOptions);
        break;
    case 'error':
        growlOptions['title'] = 'Error!';
        $.growl.error(growlOptions);
        break;
    case 'warning':
        growlOptions['title'] = 'Warning!';
        $.growl.warning(growlOptions);
        break;
    case 'info':
        growlOptions['title'] = 'Info!';
        $.growl(growlOptions);
        break;
    default:
        growlOptions['title'] = 'Warning!';
        $.growl.warning(growlOptions);
  }
}

@if (Session::get('error'))
  $(document).ready(function() {
    easyGrowl('error', "{{ Session::get('error')}}", 5000);
  });
@endif
@if (Session::get('success'))
  $(document).ready(function() {
    easyGrowl('success', "{{ Session::get('success')}}", 5000);
  });
@endif
@if (Session::get('warning'))
  $(document).ready(function() {
    easyGrowl('warning', "{{ Session::get('warning')}}", 5000);
  });
@endif

</script>
