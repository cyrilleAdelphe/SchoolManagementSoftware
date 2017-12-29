@extends('backend.'.$role.'.main')

@section('content')

<h1>Final Report of {{$exam_id}}</h1>
<h4>Name: </h4>
<h4>Class: </h4>
<h4>Section: </h4>
<h4>Roll: </h4>
<table class = "table table-responsie">
	<thead>
		<tr>
			<th>SN</th>
			<th>Subject</th>
			<th>Full Marks</th>
			<th>Pass Marks</th>
			<th>Full Marks (Pr)</th>
			<th>Pass Marks (Pr)</th>
			@foreach($weightage as $exam_id => $weight)
				<th>{{$exam_id}} (Th) ({{$weight}}%)</th>
				<th>{{$exam_id}} (Pr) ({{$weight}}%)</th>
				<th>CAS</th>
			@endforeach
			<th>Combine Obtained Marks</th>
			<th>Combine Obtained Marks Grade</th>
			<th>Combine Cas Marks</th>
			@if($config->cas_percentage > 0)
			<th>Cas + Exam Grade</th>
			@endif
		</tr>
	</thead>

	@define $i = 1
	@define $th = 0
	@define $prac = 0
	@define $combine = 0
	@define $cas = 0
	<tbody>
		@foreach($exam_conditions as $s)
		<tr>
			<td>{{$i++}}</td>
			
				<td>{{$s->subject_name}}</td>
				<td>{{$s->full_marks}}</td>
				<td>{{$s->pass_marks}}</td>
				@if($s->practical_full_marks == 0)
					<td>NA</td>
					<td>NA</td>
				@else
					<td>{{$s->practical_full_marks}}</td>
					<td>{{$s->practical_pass_marks}}</td>
				@endif
				@define $total_marks = 0
				@define $cas_total = 0
				@define $cas_count = 0

				@foreach($weightage as $exam_id => $weight)
					@define $th = $data[$exam_id]['subjects'][$s->subject_id]['marks'] * $weight / 100
					@define $total_marks += $th
					<td>{{$th}}</td>
					@define $prac = $data[$exam_id]['subjects'][$s->subject_id]['practical_marks']  * $weight / 100
					@define $total_marks += $prac
					<td>{{$prac}}</td>
					@define $cas = $data[$exam_id]['subjects'][$s->subject_id]['cas_marks']
					<td>{{$data[$exam_id]['subjects'][$s->subject_id]['cas_grade']}}</td>
					@if($cas !== 'NA')
						@define $cas_total += $cas
						@define $cas_count +=1
					@endif
				@endforeach
				<td>{{$total_marks}}</td>
				@define $percent = $total_marks / ($s->full_marks + $s->practical_full_marks) * 100  
				<td><?php echo GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $percent)->grade; ?></td>
				@if($cas_count > 0)
				@define $cas_percentage = $cas/$cas_count
				<td><?php echo GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $cas)->grade; ?></td>
				@else
				<td>NA</td>
				@endif
				@if($config->cas_percentage > 0)
				@define $combine = $cas_percentage * $config->cas_percentage / 100 + (100 - $config->cas_percentage) * $percent / 100
				<td><?php echo GradeHelperController::convertPercentageToGrade($input['session_id'], $input['class_id'], $combine)->grade; ?></td>
				@endif
		</tr>
		@endforeach
	</tbody>
</table>

<div class = "row">
	<div class = "col-md-6">
		Total :
	</div>
	<div class = "col-md-6">
		{{$summary->total_marks}}
	</div>
	<div class = "col-md-6">
		Percent (Exam and Cas Combined) :
	</div>
	<div class = "col-md-6">
		{{$summary->percentage}}
	</div>
	<div class = "col-md-6">
		CGPA :
	</div>
	<div class = "col-md-6">
		{{$summary->cgpa}}
	</div>
	<div class = "col-md-6">
		Status :
	</div>
	<div class = "col-md-6">
		{{$summary->status}}
	</div>
	<div class = "col-md-6">
		Rank :
	</div>
	<div class = "col-md-6">
		{{$summary->rank}}
	</div>
</div>

@stop