<div>
	@if(count($data['data']))
	<input type = "hidden" name = "session_id" value = "{{$data['session_id_class_id_section_id']->session_id}}">
	<input type = "hidden" name = "class_id" value = "{{$data['session_id_class_id_section_id']->class_id}}">
	<input type = "hidden" name = "section_id" value = "{{$data['session_id_class_id_section_id']->section_id}}">
	<input type = "hidden" id= "full_marks" name = "full_marks" value = "{{$full_marks}}">

	<div>
		<b>Session: {{AcademicSession::where('id', $data['session_id_class_id_section_id']->session_id)->pluck('session_name')}}
		Class: {{Classes::where('id', $data['session_id_class_id_section_id']->class_id)->pluck('class_name')}}
		Section: {{Section::where('id', $data['session_id_class_id_section_id']->section_id)->pluck('section_code')}}</b>
	</div>
	<div class = "note">
	CAS marks are in percent. Full marks is {{$full_marks}}
	</div>
	<table id="pageList" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>SN</th>
				<th>Roll</th>
				<th>Name</th>
				<th>Marks</th>
			</tr>
		</thead>
		<tbody>
			@define $i = 0
			@foreach($data['data'] as $student_id => $d)
			<tr>
				<td>{{++$i}}</td>
				<td>{{$d['current_roll_number']}}</td>
				<td>{{$d['student_name']}} {{$d['last_name']}}</td>
				<td><input type = "number" name = "exam_marks[]" value = "{{$d['sub_topic_marks']}}" step=1 class = "exam_marks"></td>
				<input type = "hidden" name = "student_id[]" value = "{{$student_id}}" step=1>
			</tr>
			@endforeach
		</tbody>
	</table>
		{{Form::token()}}
		<div class = "form-group">
			
				<input type = "submit" class = "btn btn-flat btn-success submit-enable-disable" related-form = "assignMarksForm" value = "Assign">
			
		</div>
	
	@else
		No Student Found
	@endif
</div>

