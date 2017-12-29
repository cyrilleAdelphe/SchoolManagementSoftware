
	<h1>
		@if($student)
			{{$student->student_name}} {{$student->last_name}}
		@endif
	</h1>

	@if(empty($data) === true)
		<h1>No Records Found</h1>
	@else

	<table class = 'table table-striped table-hover table-bordered'>
		<thead>
			<tr>
				<th>SN</th>
				<th>Date</th>
				<th>Status</th>
				<th>Comment</th>

			</tr>
		</thead>
		@define $i = 0
		<tbody>
			@foreach($data as $d)
			<tr>
				<td>{{++$i}}</td>
				<td>{{Carbon\Carbon::createFromFormat('Y-m-d', $d['date'])->format('Y-m-d l')}}</td>
				<td>{{$d['status']}}</td>
				<td>{{$d['comment']}}</td>
				<td>
					
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@endif

