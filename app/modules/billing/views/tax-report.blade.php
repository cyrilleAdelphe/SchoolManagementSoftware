@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Billing</h1>
@stop

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
@stop


@section('content')
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Choose date range</label>
                    <input type = 'text' name = 'daterange' id = "myDate" value="Show current date" class = 'form-control myDate required'>
                  </div>
                </div>
                
                <div class="col-sm-3">
                  <input type = "checkbox" id = "get_only_cleared" value = "true">Generate Only Cleared
                </div>

                <div class="col-sm-3">
                  <label style="color: #fff; display: block;">Generate</label>
                  
                    <input type="submit" class="btn btn-success btn-flat" id = "generate-report" value="Generate Report" />  
                  
                </div>
              </div><!-- row ends -->
              <div id = 'ajax-content'>
              </div>
              <br/>
              
            </div>
@stop

@section('custom-js')
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
        $(function() {
         
          $('.myDate').daterangepicker(
          {
              
              "showDropdowns": true,
              "showCustomRangeLabel": false,
              "alwaysShowCalendars": true,
              locale: {
                format: 'YYYY/MM/DD'
              },
              startDate: '{{date('Y/m/d')}}',
          }, 
          function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY/MM/DD') + ' to ' + end.format('YYYY/MM/DD') + ' (predefined range: ' + label + ')");
          });
        });

        $('#generate-report').click(function()
        {
          ///// Billing-v1-changed-made-here //////
        $('#ajax-content').html('<img src = " + ' + "{{ asset('sms/assets/loading.gif') }}" + '">');
        ///// Billing-v1-changed-made-here //////
          var only_cleared;
          if($('#get_only_cleared').is(':checked'))
          {
            only_cleared = 'yes';
          }
          else
          {
            only_cleared = 'no';
          }

          $.ajax(
                  {
                    'url' : '{{URL::route('billing-api-tax-report-list-view')}}',
                    'method' : 'GET',
                    'data' : {'date_range' : $('#myDate').val(), 'only_cleared' : only_cleared }
                  }).done(function(data)
                  {
                    $('#ajax-content').html(data);
                  });
        });

        
    </script>

@stop