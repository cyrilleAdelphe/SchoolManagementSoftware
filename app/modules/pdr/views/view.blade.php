@extends('pdr.views.tabs')

@section('custom-css')

@stop

@section('tab-content')
	@if($data)

			<div class="pdrInfo">
				<ul>
					<li class="@if($errors->has('pdr_date')) has-error @endif">
			        	<strong>Date :</strong> {{$data->pdr_date}}
					</li>
					<li>
						<strong>Session : </strong>{{AcademicSession::where('id', $data->session_id)->pluck('session_name')}}
					</li>
					<li>
						<strong>Class :</strong> {{Classes::where('id', $data->class_id)->pluck('class_name')}}
					</li>
					<li>
						<strong>Section :</strong> {{Section::where('id', $data->section_id)->pluck('section_code')}}
					</li>
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
							<tr>
								<td>{{$d->subject_name}}</td>
								<td>{{$d->chapter}}</td>
								<td>{{$d->class_activity}}</td>
								<td>{{$d->learning_achievement}}</td>
								<td>{{$d->homework}}</td>
								<td>{{$d->comment}}</td>
								</div>
							</tr>
						@endforeach
					</tbody>
				</table>
					

		
	@else
		<h1>No Data Found</h1>
	@endif
@stop

@section('custom-js')
@stop

