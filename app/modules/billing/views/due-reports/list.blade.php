@extends('backend.'.$role.'.main')

@section('custom-css')
  <link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="{{asset('sms/plugins/daterangepicker/daterangepicker.css')}}" />
  <link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet"/>
@stop

@section('content')
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label>Select date</label>
                    <input id="date" type = 'text' name = 'daterange' value="Show current date" class = 'form-control myDate required'>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Due by days</label>
                    <input id="due-by-days" class="form-control" type="text" value="0">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label style="color: #fff; display: block;">Generate</label>
                    <a id = "generate" href="" class="btn btn-success btn-flat">Generate report</a>
                  </div>
                </div>
              </div><!-- row ends -->
              
              <div id = "ajax-content">
              </div>

@stop

@section('custom-js')
    <script src="{{asset('sms/assets/js/lity.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
      $('.myDate').daterangepicker({
          "singleDatePicker": true,
          "locale": {
              "format": "YYYY-MM-DD",
              "separator": " - ",           
          },
          "startDate": "{{date('Y-m-d')}}"
        }, function(start, end, label) {
          console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
      });
    </script>
    <script>
    $(function()
    {
      $('#generate').click(function(e)
      {
        e.preventDefault();
        $('#ajax-content').html('loading....');
        $.ajax
        ({
          url: '{{URL::route("billing-api-remaining-due-list")}}',
          method: 'get',
          data: {'due_days':$('#due-by-days').val(), 'start_date': $('#date').val()}

        }).done(function(data)
        {
          $('#ajax-content').html(data);
        });
      });

        //var lightbox = 

        // Bind as an event handler
        $(document).on('click', '.litty-dynamic', function(e)
          {
            
            e.preventDefault();
            var href = $(this).attr('href');
            href += '&due_days=' + $('#due-by-days').val() + '&start_date=' + $('#date').val();
            $(this).attr('href' , href);
            var lightbox = lity(href);
          });
      });
    </script>
@stop