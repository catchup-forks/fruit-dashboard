<div id="reminder-wrapper" class="widget-inner text-center fill">
<form name="calc_{{ $widget['id'] }}" class="fill">
<table class="fill">
  <tr>
    <td colspan="4"> <input type="text" class="form-control" name="input" size="16">
    </td>
  </tr>
  <tr>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  1  " onclick="calc_{{ $widget['id'] }}.input.value += '1'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  2  " onclick="calc_{{ $widget['id'] }}.input.value += '2'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  3  " onclick="calc_{{ $widget['id'] }}.input.value += '3'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  +  " onclick="calc_{{ $widget['id'] }}.input.value += ' + '"></td>
  </tr>
  <tr>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  4  " onclick="calc_{{ $widget['id'] }}.input.value += '4'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  5  " onclick="calc_{{ $widget['id'] }}.input.value += '5'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  6  " onclick="calc_{{ $widget['id'] }}.input.value += '6'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  -  " onclick="calc_{{ $widget['id'] }}.input.value += ' - '"></td>
  </tr>
  <tr>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  7  " onclick="calc_{{ $widget['id'] }}.input.value += '7'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  8  " onclick="calc_{{ $widget['id'] }}.input.value += '8'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  9  " onclick="calc_{{ $widget['id'] }}.input.value += '9'"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  x  " onclick="calc_{{ $widget['id'] }}.input.value += ' * '"></td>
  </tr>
  <tr>
    <td><input type="button" class="btn btn-danger fill no-padding" value="  c  " onclick="calc_{{ $widget['id'] }}.input.value = ''"></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  0  " onclick="calc_{{ $widget['id'] }}.input.value += '0'"></td>
    <td><input type="submit" class="btn btn-success fill no-padding" value="  =  "></td>
    <td><input type="button" class="btn btn-primary fill no-padding" value="  /  " onclick="calc_{{ $widget['id'] }}.input.value += ' / '"></td>
  </tr>
</table>
</form>
</div>

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function () {
    var form = "[name='calc_{{ $widget['id'] }}']";
    var input = form + " [name='input']";
    $(form).submit( function (e) {
      e.preventDefault();
      var value = eval($(input).val());
      $(input).val(Math.round(value * 1000) / 1000);
    });
  });
</script>
@append