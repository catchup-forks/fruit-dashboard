@extends('meta.base-user')

  @section('pageTitle')
    @if($data['id'] == "au") Active Users @endif
    @if($data['id'] == "arr") Annual Run Rate @endif
    @if($data['id'] == "arpu") Average Revenue Per User @endif
    @if($data['id'] == "cancellations") Cancellations @endif
    @if($data['id'] == "mrr") Monthly Recurring Revenue @endif
    @if($data['id'] == "uc") User Churn @endif
  @stop

  @section('pageContent')

    <div id="content-wrapper">
      @parent
      <div class="col-md-12">

        <div class="row no-margin-hr panel-padding stat-header bordered">

          <div class="col-md-4 col-lg-5">
              <small><strong>CHOOSE A METRIC:</strong></small><br>
              <select class="form-control input-lg" onChange="window.location.href=this.value">
                <option value="{{ URL::route('dashboard.dashboard') }}">Back to dashboard</option>
                @if ($isFinancialStuffConnected == 1)
                  @foreach ($currentMetrics as $key => $value)
                    <option value="{{ URL::route('dashboard.single_stat', $key) }}" @if($data['id'] == $key) selected @endif>{{ $value['metricName'] }}</option>
                  @endforeach
                @endif
                @foreach ($widgets as $widget)
                  @if ($widget->widget_type=='google-spreadsheet-line-column')
                  <option value="{{ URL::route('dashboard.single_stat', $widget->id) }}" @if($data['id'] == $widget->id) selected @endif>{{ $widget->widget_name }}</option>
                  @endif
                @endforeach
              </select>
          </div>
                  
          <div class="col-md-8 col-lg-6 col-lg-offset-1">
            <small><strong>DATE</strong></small><br>
            <form class="form-inline stat-date">
              <div class="form-group col-md-5 no-padding-hr">
                <div class="input-group date" id="startDateStat">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control input-lg" value="{{ $data['dateInterval']['startDate'] }}">
                </div>
              </div>
              <div class="form-group dash col-md-1 no-padding-hr text-center">
                &ndash;
              </div>
              <div class="form-group col-md-5 no-padding-hr">
                <div class="input-group date" id="stopDateStat">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control input-lg" value="{{ $data['dateInterval']['stopDate'] }}">
                </div>
              </div>
            </form>
          </div>

        </div> <!-- / .stat-header -->

        <!-- Statistics graph -->
        <div class="row panel-body bordered">
          <div class="single-statistic-wrapper">
            <canvas id="singleStat"></canvas>
          </div>
        </div> <!-- / .row .panel-body -->

        @if ($metric_type == 'financial')
        <!-- Change numbers -->
        <div class="row panel-body margin-vr-sm bordered">
          <div class="statistic-description">
            <div class="row">
              <div class="col-md-3 stat-description-box">
                <span class="text-date"><h4>Current <small id='currentDate'>({{ $data['dateInterval']['stopDate'] }})</small></h4></span>
                @if($data['currentValue'])
                  @if($data['positiveIsGood'])
                    @if(str_contains($data['currentValue'],'-'))
                      <span id="currentValue" class="text-money down">
                    @else
                      <span id="currentValue" class="text-money up">
                    @endif
                  @else
                    @if(str_contains($data['currentValue'],'-'))
                      <span id="currentValue" class="text-money up">
                    @else
                      <span id="currentValue" class="text-money down">
                    @endif
                  @endif
                  {{ $data['currentValue'] }}
                @else
                  <span id="currentValue" class="text-money down">
                  N/A
                @endif
                </span>
              </div>
              <div class="col-md-3 stat-description-box">
                <span class="text-date"><h4>30 days ago</h4></span>
                @if($data['oneMonth'])
                  @if($data['positiveIsGood'])
                    @if(str_contains($data['oneMonth'],'-'))
                      <span id="oneMonth" class="text-money down">
                    @else
                      <span id="oneMonth" class="text-money up">
                    @endif
                  @else
                    @if(str_contains($data['oneMonth'],'-'))
                      <span id="oneMonth" class="text-money up">
                    @else
                      <span id="oneMonth" class="text-money down">
                    @endif
                  @endif
                  {{ $data['oneMonth'] }}
                @else
                  <span id="oneMonth" class="text-money down">
                  N/A
                @endif
                </span>
              </div>
              <div class="col-md-3 stat-description-box">
                <span class="text-date"><h4>6 months ago</h4></span>
                @if($data['sixMonth'])
                 @if($data['positiveIsGood'])
                    @if(str_contains($data['sixMonth'],'-'))
                      <span id="sixMonth" class="text-money down">
                    @else
                      <span id="sixMonth" class="text-money up">
                    @endif
                  @else
                    @if(str_contains($data['sixMonth'],'-'))
                      <span id="sixMonth" class="text-money up">
                    @else
                      <span id="sixMonth" class="text-money down">
                    @endif
                  @endif
                  {{ $data['sixMonth'] }}
                @else
                  <span id="sixMonth" class="text-money down">
                  N/A
                @endif
                </span>
              </div>
              <div class="col-md-3 stat-description-box">
                <span class="text-date"><h4>1 year ago</h4></span>
                @if($data['oneYear'])
                 @if($data['positiveIsGood'])
                    @if(str_contains($data['oneYear'],'-'))
                      <span id="oneYear" class="text-money down">
                    @else
                      <span id="oneYear" class="text-money up">
                    @endif
                  @else
                    @if(str_contains($data['oneYear'],'-'))
                      <span id="oneYear" class="text-money up">
                    @else
                      <span id="oneYear" class="text-money down">
                    @endif
                  @endif
                  {{ $data['oneYear'] }}
                @else
                  <span id="oneYear" class="text-money down">
                  N/A
                @endif
                </span>
              </div>
            </div> <!-- / .row -->
          </div> <!-- / .statistic-description -->
        </div>

        <div class="row panel-body margin-vr-sm bordered">
          <div class="statistic-description">
            <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>30 days growth</h4></span>
              @if($data['oneMonthChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['oneMonthChange'],'-'))
                    <span id="oneMonthChange" class="text-money down">
                  @else
                    <span id="oneMonthChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['oneMonthChange'],'-'))
                    <span id="oneMonthChange" class="text-money up">
                  @else
                    <span id="oneMonthChange" class="text-money down">
                  @endif
                @endif
                {{ $data['oneMonthChange'] }}
              @else
                <span id="oneMonthChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
            <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>60 days growth</h4></span>
              @if($data['twoMonthChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['twoMonthChange'],'-'))
                    <span id="twoMonthChange" class="text-money down">
                  @else
                    <span id="twoMonthChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['twoMonthChange'],'-'))
                    <span id="twoMonthChange" class="text-money up">
                  @else
                    <span id="twoMonthChange" class="text-money down">
                  @endif
                @endif
              {{ $data['twoMonthChange'] }}
              @else
                <span id="twoMonthChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
            <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>3 month growth</h4></span>
              @if($data['threeMonthChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['threeMonthChange'],'-'))
                    <span id="threeMonthChange" class="text-money down">
                  @else
                    <span id="threeMonthChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['threeMonthChange'],'-'))
                    <span id="threeMonthChange" class="text-money up">
                  @else
                    <span id="threeMonthChange" class="text-money down">
                  @endif
                @endif
              {{ $data['threeMonthChange'] }}
              @else
                <span id="threeMonthChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
            <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>6 month growth</h4></span>
              @if($data['sixMonthChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['sixMonthChange'],'-'))
                    <span id="sixMonthChange" class="text-money down">
                  @else
                    <span id="sixMonthChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['sixMonthChange'],'-'))
                    <span id="sixMonthChange" class="text-money up">
                  @else
                    <span id="sixMonthChange" class="text-money down">
                  @endif
                @endif
              {{ $data['sixMonthChange'] }}
              @else
                <span id="sixMonthChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
              <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>9 month growth</h4></span>
              @if($data['nineMonthChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['nineMonthChange'],'-'))
                    <span id="nineMonthChange" class="text-money down">
                  @else
                    <span id="nineMonthChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['nineMonthChange'],'-'))
                    <span id="nineMonthChange" class="text-money up">
                  @else
                    <span id="nineMonthChange" class="text-money down">
                  @endif
                @endif
              {{ $data['nineMonthChange'] }}
              @else
                <span id="nineMonthChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
            <div class="col-md-2 stat-growth-box">
              <span class="text-date"><h4>1 year growth</h4></span>
              @if($data['oneYearChange'])
                @if($data['positiveIsGood'])
                  @if(str_contains($data['oneYearChange'],'-'))
                    <span id="oneYearChange" class="text-money down">
                  @else
                    <span id="oneYearChange" class="text-money up">
                  @endif
                @else
                  @if(str_contains($data['oneYearChange'],'-'))
                    <span id="oneYearChange" class="text-money up">
                  @else
                    <span id="oneYearChange" class="text-money down">
                  @endif
                @endif
              {{ $data['oneYearChange'] }}
              @else
                <span id="oneYearChange" class="text-money down">
                N/A
              @endif
              </span>
            </div>
          </div> <!-- /.statistic-description  -->
        </div> <!-- /.row -->
        @endif


        <!-- Metric description -->
        <div class="row panel-body margin-vr-sm bordered">
          <div class="col-md-4">
            <span class="text-date"><h4>{{ $metricDetails['metricName'] }}</h4></span>
          </div>
          <div class="col-md-11">
            <p class='lead'>{{ $metricDetails['metricDescription'] }}</p>            
          </div>

        </div>


      {{--    

        <!-- hidden for development, will not be rendered on client side -->        
  
        <div class="row no-margin-hr panel-padding stat-header bordered">

          <div class="col-md-4 col-lg-5">
            <div>
              <h3>
              {{ $data['statName'] }} datatable
              </h3>
            </div>
          </div>

          <div class="col-md-8 col-lg-6 col-lg-offset-1">
            <small><strong>DATE</strong></small><br>
            <form class="form-inline">
              <div class="form-group date">
                <input type="text" class="form-control stat-input" id="startDatetable" value="{{ $data['dateInterval']['startDate'] }}">
              </div>
              <div class="form-group dash">
                -
              </div>
              <div class="form-group date">
                <input type="text" class="form-control stat-input" id="stopDatetable" value="{{ $data['dateInterval']['stopDate'] }}">
              </div>
            </form>
          </div> 

        </div> <!-- / .row .stat-header   -->

        <div class="row panel-body bordered">
          <div class="invoice-info">
            <div class="invoice-recipient">
              <strong>
                @if(Auth::user()->name)
                {{ Auth::user()->name }}
                @else
                N/A
                @endif
              </strong><br>
              @if(Auth::user()->zoneinfo)
              {{ Auth::user()->zoneinfo }}
              @else
              N/A
              @endif<br>
            </div> <!-- / .invoice-recipient -->
            <div class="invoice-total">
              CURRENT:
              <span>{{ $data['currentValue'] }}</span>
            </div> <!-- / .invoice-total -->
          </div> <!-- / .invoice-info -->
          <hr>
          <div class="invoice-table">
            <div class="table-responsive">
              <table id="sortable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th class="col-md-6">SOLD ITEM</th>
                    <th>CUSTOMERS</th>
                    <th>CURRENT MRR</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($data['detailData'] as $details)
                  <tr>
                  <td>{{$details['name']}} <span class="badge">{{ $details['price'] }}&nbsp;/&nbsp;{{ $details['interval'] }}</span></td>
                  <td class="text-center">{{ $details['count'] }}</td>
                  <td class="text-center text-money up"> {{$details['mrr']}} </td>
                  </tr>
                  @endforeach
                  
                </tbody>
              </table>
            </div>
          </div> <!-- / .invoice-table -->
        </div> <!-- /. row -->
        
        <!-- / hidden for development, will not be rendered on client side -->    
         
      --}}
      
      </div> <!-- . col-md-12 -->
    </div> <!-- / #content-wrapper -->
  @stop

  @section('pageScripts')
    {{ HTML::script('js/JQtable.js'); }}

    <script type="text/javascript">
    // datepicker start
    $(document).ready(function() {
      $('#startDateStat').datepicker({
        startDate: "{{ $data['firstDay'] }}",
        endDate: "{{ $data['dateInterval']['stopDate'] }}", 
        format: "dd-mm-yyyy",
        autoclose: true
      });
      $('#stopDateStat').datepicker({
        startDate: "{{ $data['firstDay'] }}",
        endDate: "{{ $data['dateInterval']['stopDate'] }}",
        format: "dd-mm-yyyy",
        forceParse: true,
        todayHighlight: true,
        autoclose: true
      });

      var selectedStartDate, selectedStopDate, arrayStartKey, arrayStopKey;

      // datepicker event listeners, needs work, methods doesnt work
      $('#startDateStat').datepicker().on("changeDate", function(e){
        // getting the input
        selectedStartDate = $('#startDateStat input').prop("value");
        selectedStopDate = $('#stopDateStat input').prop("value");
        
        // formatting for array keys
        arrayStartKey = getFormattedDate(selectedStartDate);
        arrayStopKey = getFormattedDate(selectedStopDate);

        // if start date is bigger than end date
        if (getFormattedDate(arrayStartKey, "unix") > getFormattedDate(arrayStopKey, "unix")){
          // update start datepicker to end datepicker value
          $(this).datepicker('update', new Date(arrayStopKey));
          //$(this).prop("value",selectedStopDate);
          // growl
          $.growl.warning({
            message: "The starting date must be before the ending date.",
            size: "large",
            duration: 5000
          });
        }
        // if start date is valid
        else {
          //METHOD DOESNT WORK, WHY?
          // update end datepicker first selectable value to start datepicker value
          $('#stopDateStat').datepicker('setStartDate', new Date(arrayStartKey));
          // create new chart
          createNewChart(arrayStartKey, arrayStopKey);

          // send event to analytics
          _gaq.push(["_trackEvent", "{{ $data['statName'].' date interval' }}" , "Start date changed"]);
          mixpanel.track('{{$data["statName"]. " start date changed"}}');
        }

      });

      $('#stopDateStat').datepicker().on("changeDate", function(e){
        selectedStartDate = $('#startDateStat input').prop("value");
        selectedStopDate = $('#stopDateStat input').prop("value");

        // formatting for array keys
        arrayStartKey = getFormattedDate(selectedStartDate);
        arrayStopKey = getFormattedDate(selectedStopDate);

        // if start date is bigger than end date
        if (getFormattedDate(arrayStartKey, "unix") > getFormattedDate(arrayStopKey, "unix")){
          // update end datepicker to start datepicker value
          $(this).datepicker('update', new Date(arrayStartKey));
          //$(this).prop("value",selectedStartDate);
          // growl
          $.growl.warning({
            message: "The ending date must be after the starting date.",
            size: "large",
            duration: 5000
          });

        }
        // if end date is valid
        else {
          //METHOD DOESNT WORK, WHY?
          // update start datepicker last selectable value to end datepicker value
          $('#startDateStat').datepicker('setEndDate', new Date(arrayStopKey));
          // create new chart
          createNewChart(arrayStartKey, arrayStopKey);

          // change the displayed metrick numbers
          changeStatNumbers(arrayStopKey);

          // send event to analytics
          _gaq.push(["_trackEvent", "{{ $data['statName'] .' date interval' }}" , "Stop date changed"]);
          mixpanel.track('{{$data["statName"]. " stop date changed"}}');
        }
      });

      // datepicker bugfix

      
      
      // CHART.JS
      //options
      var options = {
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: false,
        scaleFontFamily: "'Arial', sans-serif",
        responsive: true,
        maintainAspectRatio: false,
        bezierCurveTension : 0.1,
        pointHitDetectionRadius : 5
        @if ($data['id'] == 'mrr' || $data['id'] == 'arpu' || $data['id'] == 'arr')
          ,tooltipTemplate: "<%if (label){%><%=label%>: $<%}%><%= value %>"
        @endif
        @if ($data['id'] == 'uc')
          ,tooltipTemplate: "<%if (label){%><%=label%>:<%}%> <%= value %>%"
        @endif
      };
      var ctx = $('#singleStat').get(0).getContext("2d");
      // all labels
      var labels = [@foreach ($data['fullHistory'] as $date => $value)"{{ $date }}", @endforeach];
      // all data value
      var data = [@foreach ($data['fullHistory'] as $date => $value)@if($value == null)0,@else{{ $value }},@endif @endforeach];

      // for default view
      var data30 = {
        labels: [@foreach ($data['history'] as $date => $value)"{{ $date }}", @endforeach],
        datasets: [
            {
                label: "{{$data['statName']}}",
                fillColor: "rgba(151,187,205,0.4)",
                strokeColor: "rgba(151,187,205,0.6)",
                data: [@foreach ($data['history'] as $date => $value)@if($value == null)0,@else{{ $value }},@endif @endforeach]
            }
        ]
      };

      // drawing default chart.js
      var singleStat = new Chart(ctx).Line(data30, options);

      // creating new chart
      function createNewChart(arrayStart, arrayStop){
      if (arrayStart && arrayStop){
          // search all labels for selected data interval's start and end index value in array
          for (var i=0;i<labels.length;i++){
            if (arrayStart == labels[i]){
              arrayStart = i;
            }
            if (arrayStop == labels[i]){
              arrayStop = i;
              break;
            }
          }
          
          arrayStop = parseInt(arrayStop);
          arrayStart = parseInt(arrayStart);
          var newLabel = [];
          var newData = [];
          
          // updating label array with selected index array value
          for(i = arrayStart;i<=arrayStop;i++){
            newLabel.push(labels[i]);  
          }
          // updating data array with selected index array value
          for(i = arrayStart;i<=arrayStop;i++){
            newData.push(data[i]);  
          }

          // if dataset bigger than 30
          var finalData = [];
          var finalLabel = [];
          if (newData.length > 30){
            // find modulus for 30 points in dataset, with equal distance
            var modulus = Math.floor((newLabel.length) / 30);
            
            for(i=0; i<newData.length; i++){
              // first dataset and where modulus equals zero goes into final data
              if (i === 0 || i % modulus === 0){
                finalData.push(newData[i]);
                finalLabel.push(newLabel[i]);
              }
              if (finalData.length === 30){
                break;
              }
            }

          }
          else {
            finalData = newData;
            finalLabel = newLabel;
          }
          
          // destroying stat
          singleStat.destroy();
          

          // for updated view
          var dataFinal = {
            labels: finalLabel,
            datasets: [
                {
                    fillColor: "rgba(151,187,205,0.4)",
                    strokeColor: "rgba(151,187,205,0.6)",
                    data: finalData
                }
            ]
          };
          // creating updated stat
          ctx = $('#singleStat').get(0).getContext("2d");
          singleStat = new Chart(ctx).Line(dataFinal, options);
        }
      }

      function getFormattedDate(date,format){
        var formattedDate, d, m, y;

        if (format == "unix"){
          formattedDate = new Date(date);
          d = formattedDate.getDate();
          m =  formattedDate.getMonth();
          m += 1;  // JavaScript months are 0-11
          y = formattedDate.getFullYear();
          formattedDate = Date.UTC(y,m,d);
        }

        else {
          formattedDate = date.split('-');
          formattedDate = formattedDate.reverse().join('-');
        }

        return formattedDate;
      }

      function changeStatNumbers(date){

        // all labels
        var labels = [@foreach ($data['fullHistory'] as $date => $value)"{{ $date }}", @endforeach];
        // all data value
        var data = [@foreach ($data['fullHistory'] as $date => $value)@if($value == null)0,@else{{ $value }},@endif @endforeach];

        var positiveIsGood = @if ($data['positiveIsGood']) true @else false @endif;
        // statistic number
        var currentValue, oneMonth, sixMonth, oneYear;
        // changes
        var oneMonthChange, twoMonthChange, threeMonthChange, sixMonthChange, nineMonthChange, oneYearChange;

        // search for new current value
        var index; // currentValue index
        for(i = 0; i < labels.length; i++)
        {
          if (labels[i] == date)
          {
            index = i;
            break;
          }
        }
        // set new cuurentDate
        $('#currentDate').html('('+labels[index].split('-').reverse().join('-')+')');
        // set new current value
        currentValue = addCommas(data[index].toFixed(2));
        $('#currentValue').removeClass('down');
        $('#currentValue').removeClass('up');
        if(positiveIsGood)
        {
          if(data[index] > 0)
          {
            $('#currentValue').addClass('up');
          } else {
            $('#currentValue').addClass('down');
          }
        } else {
          if(data[index] > 0)
          {
            $('#currentValue').addClass('down');
          } else {
            $('#currentValue').addClass('up');
          }
        }
        $('#currentValue').html(currentValue);

        //set new 30 days ago
        // set correct classes
        $('#oneMonth').removeClass('up');
        $('#oneMonth').removeClass('down');
        if(index - 30 >= 0)
        {
          oneMonth = addCommas(data[index - 30].toFixed(2));
          if(positiveIsGood)
          {
            if(data[index - 30] > 0)
            {
              $('#oneMonth').addClass('up');
            } else {
              $('#oneMonth').addClass('down');
            }
          } else {
            if(data[index - 30] > 0)
            {
              $('#oneMonth').addClass('down');
            } else {
              $('#oneMonth').addClass('up');
            }
          }
        } else {
          oneMonth = 'N/A';
          $('#oneMonth').addClass('down');
        }
        $('#oneMonth').html(oneMonth);

         
        //set new 6 months ago
        $('#sixMonth').removeClass('down');
        $('#sixMonth').removeClass('up');
        if(index - 180 >= 0)
        {
          sixMonth = addCommas(data[index - 180].toFixed(2));
          if(positiveIsGood)
          {
            if(data[index - 180] > 0)
            {
              $('#sixMonth').addClass('up');
            } else {
              $('#sixMonth').addClass('down');
            }
          } else {
            if(data[index - 180] > 0)
            {
              $('#sixMonth').addClass('down');
            } else {
              $('#sixMonth').addClass('up');
            }
          }
        } else {
          sixMonth = 'N/A';
          $('#sixMonth').addClass('down');
        }
        $('#sixMonth').html(sixMonth);
        
        //set new 1 year ago
        $('#oneYear').removeClass('down');
        $('#oneYear').removeClass('up');
        if(index - 365 >= 0)
        {
          oneYear = addCommas(data[index - 365].toFixed(2));
          if(positiveIsGood)
          {
            if(data[index - 365] > 0)
            {
              $('#oneYear').addClass('up');
            } else {
              $('#oneYear').addClass('down');
            }
          } else {
            if(data[index - 365] > 0)
            {
              $('#oneYear').addClass('down');
            } else {
              $('#oneYear').addClass('up');
            }
          }
        } else {
          oneYear = 'N/A';
          $('#oneYear').addClass('down');
        }
        $('#oneYear').html(oneYear);

        //set new 30 days growth
        $('#oneMonthChange').removeClass('down');
        $('#oneMonthChange').removeClass('up');
        if (index - 30 >= 0)
        {
          if (data[index - 30] != 0)
          {
            oneMonthChange = ((data[index] / data[index - 30] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(oneMonthChange > 0)
              {
                $('#oneMonthChange').addClass('up');
              } else {
                $('#oneMonthChange').addClass('down');
              }
            } else {
              if(oneMonthChange > 0)
              {
                $('#oneMonthChange').addClass('down');
              } else {
                $('#oneMonthChange').addClass('up');
              }
            }
            oneMonthChange += '%';
          } else {
            oneMonthChange = 'N/A';
            $('#oneMonthChange').addClass('down');
          }
        } else {
          oneMonthChange = 'N/A';
          $('#oneMonthChange').addClass('down');
        }
        $('#oneMonthChange').html(oneMonthChange);
        
        //set new 60 days growth
        $('#twoMonthChange').removeClass('down');
        $('#twoMonthChange').removeClass('up');
        if (index - 60 >= 0)
        {
          if (data[index - 60] != 0)
          {
            twoMonthChange = ((data[index] / data[index - 60] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(twoMonthChange > 0)
              {
                $('#twoMonthChange').addClass('up');
              } else {
                $('#twoMonthChange').addClass('down');
              }
            } else {
              if(twoMonthChange > 0)
              {
                $('#twoMonthChange').addClass('down');
              } else {
                $('#twoMonthChange').addClass('up');
              }
            }
            twoMonthChange += '%';
          } else {
            twoMonthChange = 'N/A';
            $('#twoMonthChange').addClass('down');
          }
        } else {
          twoMonthChange = 'N/A';
          $('#twoMonthChange').addClass('down');
        }
        $('#twoMonthChange').html(twoMonthChange);

        //set new 3 months growth
        $('#threeMonthChange').removeClass('down');
        $('#threeMonthChange').removeClass('up');
        if (index - 90 >= 0)
        {
          if (data[index - 90] != 0)
          {
            threeMonthChange = ((data[index] / data[index - 90] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(threeMonthChange > 0)
              {
                $('#threeMonthChange').addClass('up');
              } else {
                $('#threeMonthChange').addClass('down');
              }
            } else {
              if(threeMonthChange > 0)
              {
                $('#threeMonthChange').addClass('down');
              } else {
                $('#threeMonthChange').addClass('up');
              }
            }
            threeMonthChange += '%';
          } else {
            threeMonthChange = 'N/A';
            $('#threeMonthChange').addClass('down');
          }
        } else {
          threeMonthChange = 'N/A';
          $('#threeMonthChange').addClass('down');
        }
        $('#threeMonthChange').html(threeMonthChange);
        
        //set new 6 months growth
        $('#sixMonthChange').removeClass('down');
        $('#sixMonthChange').removeClass('up');
        if (index - 180 >= 0)
        {
          if (data[index - 180] != 0)
          {
            sixMonthChange = ((data[index] / data[index - 180] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(sixMonthChange > 0)
              {
                $('#sixMonthChange').addClass('up');
              } else {
                $('#sixMonthChange').addClass('down');
              }
            } else {
              if(sixMonthChange > 0)
              {
                $('#sixMonthChange').addClass('down');
              } else {
                $('#sixMonthChange').addClass('up');
              }
            }
            sixMonthChange += '%';
          } else {
            sixMonthChange = 'N/A';
            $('#sixMonthChange').addClass('down');
          }
        } else {
          sixMonthChange = 'N/A';
          $('#sixMonthChange').addClass('down');
        }
        $('#sixMonthChange').html(sixMonthChange);
        
        //set new 9 months growth
        $('#nineMonthChange').removeClass('down');
        $('#nineMonthChange').removeClass('up');
        if (index - 270 >= 0)
        {
          if (data[index - 270] != 0)
          {
            nineMonthChange = ((data[index] / data[index - 270] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(nineMonthChange > 0)
              {
                $('#nineMonthChange').addClass('up');
              } else {
                $('#nineMonthChange').addClass('down');
              }
            } else {
              if(nineMonthChange > 0)
              {
                $('#nineMonthChange').addClass('down');
              } else {
                $('#nineMonthChange').addClass('up');
              }
            }
            nineMonthChange += '%';
          } else {
            nineMonthChange = 'N/A';
            $('#nineMonthChange').addClass('down');
          }
        } else {
          nineMonthChange = 'N/A';
          $('#nineMonthChange').addClass('down');
        }
        $('#nineMonthChange').html(nineMonthChange);
        
        //set new 1 year growth
        $('#oneYearChange').removeClass('down');
        $('#oneYearChange').removeClass('up');
        if (index - 365 >= 0)
        {
          if (data[index - 365] != 0)
          {
            oneYearChange = ((data[index] / data[index - 365] * 100) - 100).toFixed(1);
            if(positiveIsGood)
            {
              if(oneYearChange > 0)
              {
                $('#oneYearChange').addClass('up');
              } else {
                $('#oneYearChange').addClass('down');
              }
            } else {
              if(oneYearChange > 0)
              {
                $('#oneYearChange').addClass('down');
              } else {
                $('#oneYearChange').addClass('up');
              }
            }
            oneYearChange += '%';
          } else {
            oneYearChange = 'N/A';
            $('#oneYearChange').addClass('down');
          }
        } else {
          oneYearChange = 'N/A';
          $('#oneYearChange').addClass('down');
        }
        $('#oneYearChange').html(oneYearChange);
      }

      function addCommas(nStr)
      {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        
        var id = '{{ $data["id"] }}';
        if ( id == 'mrr' || id == 'arr' || id == 'arpu' )
        {
          return '$'+ x1 + x2;
        } else if ( id == 'au' || id == 'cancellations')
        {
          return x1;
        } else if (id == 'uc')
        {
          return x1 + x2.slice(0,2) + '%';
        }
        return 'Something is not right'
      }

    });

    </script>

    <!-- Analytics -->
    <script type="text/javascript">
      _gaq.push(["_trackEvent", "Metrics", '{{ $data["statName"]." opened" }}' ]);
      mixpanel.track("{{ $data['statName']. ' opened' }}");
    </script>
    <!-- / Analytics -->

  @stop