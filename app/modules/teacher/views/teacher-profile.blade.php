@extends('backend.teacher.main')

@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('content')
	<div class="tab-pane active" id="tab_1">
		<div class="row">
	    	<div class="col-sm-4">
	    		<div class="profile-image">
	    	 @if (File::exists(app_path(). '/modules/employee/assets/images/'. Auth::admin()->user()->id))
                <img src = "{{Config::get('app.url').'app/modules/employee/assets/images/'. Auth::admin()->user()->id}}" class="img-responsive">
              @else
                <img src="{{asset('/sms/assets/img/pic.png')}}" class="img-responsive" alt="User Image" />
              @endif	    				
	    		</div>
	    		<div class="profile-detail">
	    			<div class="main-head" style="text-align:center">
	    				{{$teacher_details->employee_name}}
	    			</div>
	    			<div class="second-head" style="text-align:center">
	    				<span style="color:#333">Username:</span> {{$teacher_details->username}}
	    			</div>
	    			
	    			<ul>
	    				<li>
	    					<label>Email:</label> {{$teacher_details->email}}
	    				</li>
	    				<li>
	    					<label>DOB(in AD):</label>{{$teacher_details->employee_dob_in_ad}} A.D.
	    				</li>
	    				<li>
	    					<label>DOB(in BS):</label>{{$teacher_details->employee_dob_in_bs}} B.S.
	    				</li>
	    				
	    				<li>
	    					<label>Gender:</label> {{$teacher_details->sex}}
	    				</li>
	    				<li>
	    					<label>Current address:</label> {{$teacher_details->current_address}}
	    				</li>
	    				<li>
	    					<label>Permanent address:</label> {{$teacher_details->permanent_address}}
	    				</li>
	    				<li>
	    					<label>Primary contact:</label> {{$teacher_details->primary_contact}}
	    				</li>
	    				<li>
	    					<label>Secondary contact:</label> {{$teacher_details->secondary_contact}}
	    				</li>
	    				<li>
	    					<label>Active:</label>{{$teacher_details->is_active}}
	    				</li>
	    			</ul>
	    		</div>
	    	</div>
	    	<div class="col-sm-8">
	    		<div class=" profile-menu" style="margin-bottom:15px">
	    			<a class="btn btn-profile btn-info btn-flat" id="dynamicClasses">
							<i class="fa fa-address-card-o" aria-hidden="true"></i>
							Classes
					</a>
	    			
					
		    	</div>
	    		<div id = "dynamicContent">
	    		</div>
	    	</div>
	  </div>

	 <input type="hidden" id="academic-session" value="{{ $current_session_id}}">
     
@stop

@section('custom-js')
	<script type="text/javascript">
		function updateDynamic(apiUrl) {
			var academic_session = $('#academic-session').val();
						
			$('#dynamicContent').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
			$.ajax({
				'url': apiUrl,
				'data': {
					'teacher_id': '{{ $teacher_details->id}}',
					'academic-session': academic_session
				},

				'method': 'GET'
			}).done(function(data) {
				$('#dynamicContent').html(data);
				$('a[disabled]').hide();
			});

		}

		$(function() {
			// on load the document list (and message form) should be loaded
			/*updateDynamic('{{ URL::route('teacher-ajax-class-view') }}');*/
			
			$('#dynamicClasses').click(function() {
				updateDynamic('{{ URL::route('teacher-ajax-class-view') }}');
			});
			
			
		});

	</script>

@stop