<?php
	$class_name = Classes::where('id', $class_id)->pluck('class_name');
	$exam_name = ExamConfiguration::where('id', $exam_id)->pluck('exam_name');
	Excel::create($class_name.' - '.$exam_name, function($excel) use ($data, $class_name, $exam_name)
	{
?>

@foreach($data['section_data'] as $section_id => $subject_ids)
	<?php $excel->sheet(Section::where('id', $section_id)->pluck('section_code'), function($sheet) use ($data, $section_id, $subject_ids, $class_name, $exam_name)
		{ ?> 

		
	<p><b>Marks Ledger of {{$exam_name}} - {{$class_name}} {{Section::where('id', $section_id)->pluck('section_code')}}</b></p>
	<table>
		<thead>
			<tr>
				<th>SN</th>
				<th>Name</th>
				@foreach($subject_ids as $subject_id => $subject)
				<th>{{$subject['subject_name']}} FM: {{$subject['full_marks']}} PM: {{$subject['pass_marks']}}</th>
				@if($subject['is_graded'] == 'yes')
					<th>{{$subject['subject_name']}} Practical FM: {{$subject['practical_full_marks']}} PM: {{$subject['practical_pass_marks']}}</th>
				@endif
				@endforeach
				
				<th>Total Marks</th>
				<th>Percentage</th>
				<th>Grade</th>
				<th>Rank</th>
				<th>Status</th>
			</tr>
			@define $i = 0


			@foreach($data['data'][$section_id] as $student_id => $d)
			
				<tr>
					<td>{{++$i}}</td>
					<td>{{$d['student_name']}}</td>
					
					@foreach($subject_ids as $subject_id => $subject)
						<td>
							@if(isset($d[$subject_id]['marks'])) 
								{{$d[$subject_id]['marks']}} 
							@else 
								Marks not given 
							@endif
						</td>
						@if($subject['is_graded'] == 'yes')
							@if($subject['practical_full_marks'])
								<td>
									@if(isset($d[$subject_id]['practical_marks']))
							 			{{$d[$subject_id]['practical_marks']}} 
							 		@else 
							 			Marks not given 
									@endif
								</td>
							@endif
						@endif
					@endforeach
					<td>{{$d['total_marks']}}</td>
					<td>{{$d['percentage']}}</td>
					<td>{{$d['overall_grade']}}</td>
					<td>{{$d['rank']}}</td>
					<td>{{$d['status']}}</td>
				</tr>
			@endforeach
		</thead>
	</table>
	<?php }); ?>

@endforeach

<?php 

})->download('xls');
?>