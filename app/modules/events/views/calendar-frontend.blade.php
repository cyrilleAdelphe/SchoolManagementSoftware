@extends('frontend.main')

@section('custom-css')
  <link href="{{asset('sms/plugins/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('sms/plugins/fullcalendar/fullcalendar.print.css')}}" rel="stylesheet" type="text/css" media='print' />
@stop

@section('page-header')
	<h1>Annual Academic Calendar</h1>
@stop

@section('content')
	<div id="calendar"></div>
@stop

@section('custom-js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/fullcalendar/fullcalendar.min.js') }}" type="text/javascript"></script>
  
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