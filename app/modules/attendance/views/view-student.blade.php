@extends('attendance.views.tabs')

@section('tab-content')
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
				<th>Action</th>
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
					<button type = "submit" class="btn btn-danger btn-flat del" type="button" @if(AccessController::checkPermission('attendance', 'can_delete') == false) disabled @endif>
					<form method = "post" action = "{{URL::route('attendance-delete-attendance-file-post', [$d['date'].'_'.$class_id.'_'.$section_code.'.csv'])}}">
						
						<i class="fa fa-fw fa-trash"></i>
						{{Form::token()}}
					</form>
					</button>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@endif

@stop

@section('custom-js')
	<script>
		$(function()
		{
			$(document).on('click', '.del', function()
			{
			    if (!confirm("Warning! If you click OK then attendance record of all the students of this class and section will be deleted for this day"))
			    {
			      return false;
			    }
			    else
			    {
			    	$(this).attr('disabled', true);
			    	$(this).find('form').submit();
			    }
		  	});
		});
	</script>
@stop
