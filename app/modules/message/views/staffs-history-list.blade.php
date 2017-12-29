@extends('message.views.tabs')

@section('custom-css')
<link href="{{asset('sms/assets/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->    
    <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
    
@stop

@section('tab-content')
  <h1>Messages Sent By</h1>
  <label class="text-green">Filter</label>
                            <div class="row">
                              <div class="col-sm-4">
                                <div class="form-group">
                                  <select class="form-control" id = "message_from">
                                    <option value = "student" @if($message_from == 'student') selected @endif>Students</option>
                                    <option value = "guardian" @if($message_from == 'guardian') selected @endif>Parents</option>
                                    <option value = "admin" @if($message_from == 'admin') selected @endif>Staff</option>
                                    <option value = "superadmin" @if($message_from == 'superadmin') selected @endif>Superadmin</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-sm-4">
                                
                                <input type = "hidden" id = "calendar_range" value = "{{Input::get('calendar_range', '')}}">
                              </div>
                              <div class="col-sm-4">
                              </div>
                            </div>

  <div id = "message_list" class = "content">                            
	@include('message.views.ajax.staffs-history-list')
  </div>

@stop

@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript">
  
</script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript">
  
</script>

    <script type="text/javascript">
      $(function() {

          function ajaxRequest(page)
          {
            $('#message_list').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>')
            $.ajax({
                "url": "{{URL::route('message-ajax-staffs-history-list')}}",
                "data": {
                          "message_from" : $('#message_from').val(), 
                          "calendar_range" : $('#calendar_range').val(),
                          "paginate" : {{Input::get('paginate', 10)}},
                          "page" : page
                        },
                //"dataType" : "json",
                "method": "GET",
              }).done(function(data) {
                  $('#message_list').html(data);
              });
          }

          function cb(start, end) {
              $('.reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#calendar_range').val($('#calendar').text());

              ajaxRequest({{Input::get('page', 1)}});

          }
          cb(moment().subtract(7, 'days'), moment());

          $('.reportrange').daterangepicker({
              ranges: {
                 'Today': [moment(), moment()],
                 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                 'This Month': [moment().startOf('month'), moment().endOf('month')],
                 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
          }, cb);

      
      $('#message_from').change(function(e)
      {
        ajaxRequest({{Input::get('page', 1)}});
      });
      
      $('body').on('click', '.custom_pagination', function(e) 
      {
      
        var url = $(this).attr('href');
        e.preventDefault();
        $('.message_list').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>')
            $.ajax({
                "url": url,
               
                //"dataType" : "json",
                "method": "GET",
              }).done(function(data) {
                  $('#message_list').html(data);
              });

      });
    
  });
  </script>
@stop