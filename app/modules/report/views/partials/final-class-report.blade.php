<div class = "content">
@if(count($data))
	<a href = "{{URL::route('report-mass-print')}}?exam_id={{Input::get('exam_id',0)}}&class_id={{Input::get('class_id', 0)}}&section_id={{Input::get('section_id', 0)}}&is_final=yes" class = "btn btn-success">Mass Print</a> 
	<table class = 'table table-bordered table-striped last-hide'>
		<thead>
			<tr>
				<th>Roll</th>
				<th>Name</th>
				<th>Percentage</th>
				<th>Rank</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $d)
			<tr>
				<td>{{$d->current_roll_number}}</td>
				<td>{{$d->student_name}}</td>
				<td>{{$d->percentage}}</td>
				<td>@if($d->status == 'Passed') {{$d->rank}} @else {{$d->status}} @endif</td>
				<td><a href = "{{URL::route('report-mass-print')}}?exam_id={{Input::get('exam_id',0)}}&student_id={{$d->student_id}}&class_id={{Input::get('class_id', 0)}}&section_id={{Input::get('section_id', 0)}}&is_final=yes"> View </a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<a href = "{{URL::route('report-mass-print')}}?exam_id={{Input::get('exam_id',0)}}&class_id={{Input::get('class_id', 0)}}&section_id={{Input::get('section_id', 0)}}&is_final=yes" class = "btn btn-success">Mass Print</a>
@else
	Report Not Generated
@endif
</div>