@extends('backend.'.$role.'.main')

@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('content')
	<div class="tab-pane active" id="tab_1">
    @if($data['data'])
	  	<div class="row" style="margin-bottom:15px">
	        <div class="col-sm-3 col-xs-2" >
	          <a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-arrow-left "></i> Go Back</a>
	        </div><!-- row ends -->
	        <div class="col-sm-3 col-sm-offset-6 col-xs-offest-1 col-xs-9" >
	        	<div class="btn-group pull-right">
					
					@if(Auth::user()->check() && Auth::user()->user()->role == 'student')
					<button class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
						<i class="fa fa-fw fa-cog"></i>
						
					</button>
					
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="#" data-toggle="modal" data-target="#details">Edit details</a>
						</li>
						<li>
							<a href="#" data-toggle="modal" data-target="#password">Change password</a>
						</li>
					</ul>
					@endif
				</div>
	        </div>
	    </div>
	  
	    @if(Auth::user()->check() && Auth::user()->user()->role == 'student')
		    @include('student.views.change-password-modal')
			@include('student.views.edit-modal')
		@endif

		<div class="row">
	    	<div class="col-sm-4">
	    		<div class="profile-image">
	    			@if(strlen(trim($data['data']->photo)))
					<img class="img-responsive" src = "{{Config::get('app.url').'app/modules/student/assets/images/'.$data['data']->photo}}" >
					@else
						<img class="img-responsive" src = "{{Config::get('app.url').'app/modules/student/assets/images/no-img.png'}}" >
					@endif
	    		</div>
	    		<div class="profile-detail">
	    			<div class="main-head" style="text-align:center">
	    				{{$data['data']->student_name}}
	    			</div>
	    			<div class="second-head" style="text-align:center">
	    				<span style="color:#333">Username:</span> {{$data['data']->username}}
	    			</div>
	    			<ul>	<li>
	    					<label> Last Name:</label> {{$data['data']->last_name}}
	    				</li>
	    				<li>
	    					<label>Email:</label> {{$data['data']->email}}
	    				</li>
	    				<li>
	    					<label>DOB:</label>{{$data['data']->dob_in_ad}} <span class="text-green">A.D. {{$data['data']->dob_in_bs}} B.S.</span>
	    				</li>
	    				<li>
	    					<label>Registered session:</label> {{$data['data']->session_name}}
	    				</li>
	    				<li>
	    					<label>Class:</label> {{$data['data']->class_name}} {{$data['data']->registered_section_code}}
	    				</li>
	    				<li>
	    					<label>Gender:</label> {{$data['data']->sex}}
	    				</li>
	    				<li>
	    					<label>House Name:</label> {{$data['house_name']}}
	    				</li>
	    				<li>
	    					<label>Ethnicity: </label> {{$data['ethnicity_name']}}
	    				</li>
	    				<li>
	    					<label>Current address:</label> {{$data['data']->current_address}}
	    				</li>
	    				<li>
	    					<label>Permanent address:</label> {{$data['data']->permanent_address}}
	    				</li>
	    				<li>
	    					<label>Guardian contact:</label> {{$data['data']->guardian_contact}}
	    				</li>
	    				<li>
	    					<label>Secondary contact:</label> {{$data['data']->secondary_contact}}
	    				</li>
	    				<li>
	    					<label>Active:</label> {{$data['data']->is_active}}
	    				</li>
	    			</ul>
	    		</div>
	    	</div>
	    	<div class="col-sm-8">
	    		<div class=" profile-menu" style="margin-bottom:15px">
	    			<a class="btn btn-profile btn-info btn-flat" id="dynamicDocument">
							<i class="fa fa-file-o"></i>
							Docs
					</a>
	    			
					<a class="btn btn-profile btn-primary btn-flat" id="dynamicExam">
							<i class="fa fa-graduation-cap"></i>
							Exam Reports
					</a>
					<a class="btn btn-profile bg-purple btn-flat" id="dynamicLibrary">
							<i class="fa fa-book"></i>
							Library History
					</a>
					<a class="btn btn-profile btn-success btn-flat" id="dynamicExtraActivities">
							<i class="fa fa-rocket"></i>
							Extra Activities
					</a>
					
					<a class="btn btn-profile btn-warning btn-flat" id="dynamicPayment">
							<i class="fa fa-dollar"></i>
							Payments
					</a>
					<a class="btn btn-profile btn-danger btn-flat" id="dynamicAttendance">
							<i class="fa fa-bar-chart"></i>
							Attendance
					</a>						
		    	</div>
	    		<div id = "dynamicContent">
	    		</div>
	    	</div>
	  </div>
		  @define $i = 1
					
			<div class="main-head">
	  		Related to
	  	</div>

			<table id="pageList" class="table table-bordered table-striped">
			 	<thead>
			 		<tr>
			 			<th>SN.</th>
			 			<th>Guardian's name</th>
			 			<th>Relation</th>
			 		</tr>
			 	</thead>

			 	<tbody>
				@foreach($data['related_guardians'] as $guardian)			
					<tr>
						<td>
							{{$i++}}
						</td>
						<td>
							<a href="{{ URL::route('guardian-view', $guardian->id) }}">{{$guardian->guardian_name}}</a>
						</td>
						<td>
							{{ $guardian->relationship }}
						</td>
					</tr>
				@endforeach
						
				</tbody>
			</table>
			@else
				<h1>Record Not Found for Current Session</h1>
			@endif
                      
	</div>
     
@stop

@section('custom-js')
	<script type="text/javascript">
		function updateDynamic(apiUrl) {
			$('#dynamicContent').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
			$.ajax({
				'url': apiUrl,
				'data': {
					'student_id': '{{ isset($data['data']->id) ? $data['data']->id : 0  }}'
				},
				'method': 'GET'
			}).done(function(data) {
				$('#dynamicContent').html(data);
				$('a[disabled]').hide();
			});
		}

		$(function() {
			// on load the document list (and message form) should be loaded
			updateDynamic('{{ URL::route('student-ajax-document-list') }}');
			
			$('#dynamicDocument').click(function() {
				updateDynamic('{{ URL::route('student-ajax-document-list') }}');
			});
			
			$('#dynamicPayment').click(function() {
				updateDynamic('{{ URL::route('student-ajax-payments') }}');
			});
			
			$('#dynamicExam').click(function() {
				updateDynamic('{{ URL::route('student-ajax-exam-report') }}');
			});

			$('#dynamicExtraActivities').click(function() {
				updateDynamic('{{ URL::route('student-ajax-extra-activities') }}');
			});

			$('#dynamicLibrary').click(function() {
				updateDynamic('{{ URL::route('student-ajax-library') }}');
			});
			
			$('#dynamicAttendance').click(function() {
				updateDynamic('{{ URL::route('student-ajax-attendance-select-month') }}');
			});
			
		});

	</script>

@stop