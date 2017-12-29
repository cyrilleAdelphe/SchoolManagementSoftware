@extends('events.views.backend')

@section('custom-css')
	  <!-- iCheck for checkboxes and radio inputs -->
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />

    <!-- Date Picker -->
    <link href="{{asset('sms/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
    
    <!-- Daterange picker -->
    <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <link href="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('sms/plugins/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('sms/plugins/fullcalendar/fullcalendar.print.css')}}" rel="stylesheet" type="text/css" media='print' />


@stop

@section('page-header')
	<h1>Annual Academic Calendar</h1>
@stop

@section('tab-content')
	<div id="calendar"></div>
@stop

@section('custom-js')
	 <!-- Page script -->
    <script src="{{asset('sms/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
    
    <!-- InputMask -->
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js') }}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js') }}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.extensions.js') }}" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/fullcalendar/fullcalendar.min.js') }}" type="text/javascript"></script>
    
    
    <!-- DATA TABES SCRIPT -->
    <script src="{{asset('sms/plugins/datatables/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/datatables/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
      $(function () {
        $("#pageList").dataTable();
        
      });
    </script>

    <!-- Editor SCRIPT -->
    <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
      });
    </script>

    <script type="text/javascript">
      $(function () {

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date();
        var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear();
        $('#calendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
          },
          events: {{$events}}
          
        });
      });
    </script>
@stop