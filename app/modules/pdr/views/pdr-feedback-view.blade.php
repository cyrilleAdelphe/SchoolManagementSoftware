@extends('pdr.views.tabs')

@section('custom-css')

@stop

@section('tab-content')
	@if($data)
		@define $pdr_data = Pdr::where('id', $data->pdr_id)->first()
			<div class="pdrFeedInfo">
				<ul>
					<li><stromg>Date :</stromg> {{$pdr_data->pdr_date}}</li>
					<li><strong>Session :</strong> {{AcademicSession::where('id', $pdr_data->session_id)->pluck('session_name')}}</li>
					<li><strong>Class : </strong>{{Classes::where('id', $pdr_data->class_id)->pluck('class_name')}}</li>
					<li><strong>Section :</strong> {{Section::where('id', $pdr_data->section_id)->pluck('section_code')}}</li>
					
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
						@define $json_details = json_decode($pdr_data->pdr_details)
					
						@foreach($json_details as $index => $d)
						@define $status = true
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
				<div class="pdfFeed">
					<ul>
						<li><strong><span class="text-red">Feedback for :</span></strong> {{StudentRegistration::where('id', $data->student_id)->pluck('student_name')}}</li>
						<li><strong><span class="text-green">Feedback by :</span></strong> {{Guardian::where('id', $data->guardian_id)->pluck('guardian_name')}}</li>
					</ul>
				</div>
				<div class="feedback">
					<div class = "row">
						<div class="col-sm-1"><strong>Feedback:</strong></div>
						<div class="col-sm-11"> {{$data->feedback}}</div>
					</div>
				</div>
			
					
				@if(!$status)
					<h1>Please Creat Daily Routine First</h1>
				@endif


		
	@else
		<h1>No Data Found</h1>
	@endif
@stop

@section('custom-js')
@stop

