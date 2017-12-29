@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Billing</h1>
@stop


@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
	<form method = "GET" action = "{{URL::route('billing-income-report-student')}}" id="showStudents">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Choose date range</label>
                    <input type = 'text' name = 'daterange' value="Show current date" class = 'form-control myDate required' id = "date">
                  </div>
                </div>
              
                <div class="col-sm-1">
                  <label style="color: #fff; display: block;">Go</label>
                    
                   <a href="#" class="btn btn-success btn-flat" value="Go" id = "show-income-report-button" >Go</a>
                  
                </div>
              </div><!-- row ends -->
              <div id = "ajax-content">
              </div>
              <br/>
              </form>
            <!-- <form method="post">  
                <input type="submit" name="export_pdf" class="btn bg-purple btn-flat" value="Export PDF" />
              </form>-->
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
    </script>
    <script>


      $('#show-income-report-button').click(function()
      {
        var date_range = $('#date').val();
        ///// Billing-v1-changed-made-here //////
        $('#ajax-content').html('<img src = " + ' + "{{ asset('sms/assets/loading.gif') }}" + '">');
        ///// Billing-v1-changed-made-here //////
        $.ajax({
          'url' : '{{URL::route('billing-api-get-income-report-list-view')}}',
          'method' : 'GET',
          'data' : {'date_range' : date_range}
        }).done(function(data)
        {
          $('#ajax-content').html(data);
        });
      })

      
    </script>
    
     <script type="text/javascript">
      $(function()
      {
        $(document).on('click', '.submit', function(e)
        {
        e.preventDefault();
        var class_section  = $(this).parent().parent().parent().find('.class_section').val();
        
        $('#ajax-content').append('<input type = "hidden" name = "class_section_" value = "'+class_section+'" >');
        $('#showStudents').submit();
        });
      })
     
    </script>
@stop