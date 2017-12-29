@extends('backend.'.$role.'.main')

@section('custom-css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
@stop


@section('content')
	<form action = "{{URL::route('report-enter-remarks-post')}}" method="post" >
	@foreach($data as $student_id => $d)
		@define $json = json_decode($d['exam_details'])
		
		<p>{{$json->personal_details->student_name}}</p>
		<p>{{$json->personal_details->roll}}</p>
		<p>{{sprintf('%0.2f', $d['percentage'])}}</p>
		<p>{{$d['grade']}}</p>
		<p>{{sprintf('%0.2f', $d['cgpa'])}}</p>
		<p>{{$d['status']}}</p>
		<p>{{$d['rank']}}</p>
		<div class = "remarks">
			<input type = "text" class = "remarks_no auto">
			<textarea class = "remarks_value form-control" name = "remarks[{{$student_id}}]">{{$remarks[$student_id]['remarks']}}</textarea>
		</div>

		<div>
			<label>Attendance</label>
			<input type = "text" name = "attendance[{{$student_id}}]" value = "{{$remarks[$student_id]['attendance']}}">
		</div>

	@endforeach
	<input type = "hidden" name = "report_type" value = "terminal">
	<input type = "hidden" name = "session_id" value = "{{$session_id}}">
	<input type = "hidden" name = "class_id" value = "{{$class_id}}">
	<input type = "hidden" name = "section_id" value = "{{$section_id}}">
	<input type = "hidden" name = "exam_id" value = "{{$exam_id}}">
	<input type = "submit" class = "btn btn-success" value = "Submit">
	{{Form::token()}}
	</form>

@stop

@section('custom-js')

	<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
	<script>
	$(document).on('focus.autocomplete', '.auto', function()
      {
      	var parent = $(this).parent();
      	var current_element = $(this);
        $(this).autocomplete({select: function( event, ui ) 
                {
                 // console.log(ui);
                 parent.find('.remarks_value').text(ui.item.id);
                 current_element.val(' ');
                 //updateRemainingDueList(ui.item.id);
                  //console.log(event);
                },
        source: "{{URL::route('report-ajax-get-remarks')}}"
        
        });
      });
      </script>
@stop