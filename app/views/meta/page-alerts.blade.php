@if (Session::get('error'))
  <script type="text/javascript">
    $(document).ready(function() {
      $.growl.error({
        message: "{{ Session::get('error')}}",
        size: "large",
        duration: 5000,
        location: "br"
      });
    });
  </script>
@endif

@if (Session::get('success'))
  <script type="text/javascript">
    $(document).ready(function() {
      $.growl.notice({
        title: "Success!",
        message: "{{ Session::get('success')}}",
        size: "large",
        duration: 5000,
        location: "br"
      });
    });
  </script>
@endif

@if (Session::get('warning'))
  <script type="text/javascript">
    $(document).ready(function() {
      $.growl.warning({
        title: "Warning!",
        message: "{{ Session::get('warning')}}",
        size: "large",
        duration: 5000,
        location: "br"
      });
    });
  </script>
@endif