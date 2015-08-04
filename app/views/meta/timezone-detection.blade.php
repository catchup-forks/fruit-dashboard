@if (!Session::get('timeZone'))
  <script type="text/javascript">
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: "{{ route('settings.timezone') }}",
      data: "timeZone=" + jstz.determine().name(),
    });
  </script>
@endif