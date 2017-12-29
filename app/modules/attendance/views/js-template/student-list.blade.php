@if(count($students)==0)
	No Students in the class
@else
	@foreach($students as $student)
		{{$student['current_roll_number']}}  {{$student['name']}}<br/>
	@endforeach
@endif
