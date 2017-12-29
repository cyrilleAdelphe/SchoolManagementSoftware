@extends('pdr.views.tabs')

@section('custom-css')

@stop

@section('tab-content')
	@if($data)
		<form method = "post" action = "{{URL::route('pdr-edit-post', [$data->id])}}"  id = "backendForm">
			<div class="pdrFeedInfo">
				<ul>
					<li class=" @if($errors->has('pdr_date')) has-error @endif">
			        	<strong>Date: </strong>{{$data->pdr_date}}</li>
			        <li><strong>Session :</strong> {{AcademicSession::where('id', $data->session_id)->pluck('session_name')}}</li>
			        <li><strong>Class : </strong>{{Classes::where('id', $data->class_id)->pluck('class_name')}}</li>
			        <li><strong>Section : </strong>{{Section::where('id', $data->section_id)->pluck('section_code')}}</li>
				</ul>
			</div>

			<div id = "ajax-content">
				@define $status = false
			
				<table class="table table-bordered table-striped">
				 	<thead>
				 		<tr>
				 			<th>Subject</th>
				 			<th>Chapter</th>
				 			<th>Class Activity</th>
				 			<th>Learning Achievement</th>
				 			<th>Homework</th>
				 			<th>Comment</th>
				 		</tr>
				 	</thead>
				 	<tbody>
					@define $json_details = json_decode($data->pdr_details)
					
					@foreach($json_details as $index => $d)
						@define $status = true
						<tr>
							<td>
								<label class = "control-label">{{$d->subject_name}}</label>
								<input type = "hidden" name = "subject_name[]" value = "{{$d->subject_name}}">
							</td>
							<td>
								<input type = "text" class = "form-control" name = "chapter[]" value = "{{$d->chapter}}">
							</td>
							<td>
								<input class = "form-control" name = "class_activity[]" value = "{{$d->class_activity}}">
							</td>
							<td>
								<input class = "form-control" name = "learning_achievement[]" value = "{{$d->learning_achievement}}">
							</td>
							<td>
								<input class = "form-control" name = "homework[]"  value = "{{$d->homework}}">
							</td>
							<td>
								<input class = "form-control" name = "comment[]"  value = "{{$d->comment}}">
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				@if($status)
				<div >
					{{Form::token()}}
					<input type = 'hidden' name = 'is_active' value = 'yes'>
					<input type = "submit" value = "Edit" class = "btn btn-primary btn-flat btn-lg">
				</div>
				@endif
				

				@if(!$status)
					<h1>Please Creat Daily Routine First</h1>
				@endif


		
		</form>
	@else
		<h1>No Data Found</h1>
	@endif
@stop

@section('custom-js')
@stop

