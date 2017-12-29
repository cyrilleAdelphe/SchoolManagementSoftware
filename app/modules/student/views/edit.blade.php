@extends('backend.'.$role.'.main')
@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/assets/css/nepali.datepicker.v2.2.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Edit Details</h1>
@stop
@section('content')
<div class="row">
  <div class="col-sm-3" style="margin-bottom:15px">
    <a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
  </div><!-- row ends -->
</div>

<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-edit-post',$data->id) }}"   id = "backendForm" enctype = "multipart/form-data">
			<div class="row">
				<div class="col-sm-2">
					<div class = 'form-group @if($errors->has("current_session_id")) {{"has-error"}} @endif'>
						<label for = 'name'  class = 'control-label'>Current Session:</label>
						{{HelperController::generateSelectList('AcademicSession', 
																					

					'session_name', 
																					

					'id', 
																					

					'current_session_id', 
$data->current_session_id																		

								
																					

				)}}
						<span class = 'help-block'>@if($errors->has('current_session_id')) {{$errors->first('registered_session_id')}} 

@endif</span>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("student_name")) {{"has-error"}} @endif'>
						<label for = 'student_name'  class = 'control-label'>First Name :</label>						
						<input type = 'text' name = 'student_name' value= '{{$data->student_name}}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('student_name')) {{$errors->first('student_name')}} @endif</span>		

					
					</div>
				</div>
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("last_name")) {{"has-error"}} @endif'>
						<label for = 'last_name'  class = 'control-label'>Last Name :</label>							
						<input type = 'text' name = 'last_name' value= '{{$data->last_name}}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('last_name')) {{$errors->first('last_name')}} @endif</span>			

				
					</div>
				</div>
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("sex")) {{"has-error"}} @endif'>
						<label for = 'sex'  class = 'control-label'>Gender :</label>
							
						{{HelperController::generateStaticSelectList(array('male' => 'Male', 'female' => 'Female', 'other' => 'Other'), 'sex', 

$data->sex)}}
						<span class = 'help-block'>@if($errors->has('sex')) {{$errors->first('sex')}} @endif</span>
							
					</div>
				</div>
				
			</div>

			<div class="row">
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("current_roll_number")) {{"has-error"}} @endif'>
						<label for = 'current_roll_number'  class = 'control-label'>Roll Number :</label>
							
						<input type = 'text' name = 'current_roll_number' value= '{{$data->current_roll_number}}' class = 'form-control 

required'>
						<span class = 'help-block'>@if($errors->has('current_roll_number')) {{$errors->first('current_roll_number')}} 

@endif</span>
							
					</div>
				</div>
				
				<div class="col-sm-3">
					<div id = "div_for_current_class_id">				
						<div class = 'form-group @if($errors->has("current_class_id")) {{"has-error"}} @endif'>
							<label for = 'current_class_id'  class = 'control-label'>Current Class:</label>
							
							{{HelperController::generateSelectList('Classes', 
																					

					'class_name', 
																					

					'id', 
																					

					'current_class_id', 
$data->current_class_id																		

								
																					

				)}}
							
							
								
							<span class = 'help-block'>@if($errors->has('current_class_id')) {{$errors->first('current_class_id')}} 

@endif</span>
						</div>
						
					</div>
				</div>

				
				<div class="col-sm-3">
					<div id = "div_for_current_section_code">				
							<div class = 'form-group @if($errors->has("current_section_code")) {{"has-error"}} @endif'>
								<label for = 'current_section_code'  class = 'control-label'>Current Section:</label>
									<select id = "current_section_code" name = "current_section_code" class = "form-control">
								<option value = "{{$data->current_section_code}}">{{$data->current_section_code}}</option>
							</select>
								<span class = 'help-block'>@if($errors->has('current_section_code')) {{$errors->first

('current_section_code')}} @endif</span>
							</div>						
					</div>		
				</div>
				
				<div class="col-sm-3">
					<div class = 'form-group '>
						<label for = 'house_id'  class = 'control-label'>House:</label>
							<select name = "house_id" class = "form-control">
							
								@foreach($house as $key=>$value)
									<option value="{{$key}}" @if($key==$data->house_id) selected @endif>{{$value}}</option>
								@endforeach
							</select>
						<span class = 'help-block'></span>
					</div>
				</div>
			</div>

			
			<div class="row">
				<div class="col-sm-4">
					<div class = 'form-group '>
						<label for = 'ethnicity_id'  class = 'control-label'>Ethnicity:</label>
						<select name = "ethnicity_id" class = "form-control">
							
								@foreach($ethnicity as $key=>$value)
									<option value="{{$key}}" @if($key==$data->ethnicity_id) selected @endif>

									{{$value}}</option>
								@endforeach
							</select>

						<span class = 'help-block'></span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group '>
						<label for = 'dob_in_ad'  class = 'control-label'>DOB (B.S.) </label>
						<div class="input-group">
		                 	<div class="input-group-addon"><i class="fa fa-calendar"></i></div>	
							<input class="form-control" type="text" id="nepaliDate9" value="{{ $data->dob_in_bs}}"/>
						</div>					
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("dob_in_ad")) {{"has-error"}} @endif'>
						<label for = 'dob_in_ad'  class = 'control-label'>DOB(A.D.) :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>	
							<input class="form-control"  type="text" id="englishDate9" name = 'dob_in_ad' value="{{ $data->dob_in_ad}}"  />
							<span class = 'help-block'>@if($errors->has('dob_in_ad')) {{$errors->first('dob_in_ad')}} @endif</span>
						</div>
					</div>
				</div>
			</div>
			

			<div class="row">
				<div class="col-sm-6">
					<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
						<label for = 'current_address'  class = 'control-label'>Current Address :</label>
							
						<input type = 'text' name = 'current_address' value= '{{ $data->current_address }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-6">
					<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
						<label for = 'permanent_address'  class = 'control-label'>Permanent Address :</label>
							
						<input type = 'text' name = 'permanent_address' value= '{{ $data->permanent_address }}' class = 'form-control 

required'>
						<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
							
					</div>
				</div>
				
			</div>

			<div class = "row">
				<div class="col-sm-2">
						<div class = 'form-group @if($errors->has("registered_session_id")) {{"has-error"}} @endif'>
							<label for = 'name'  class = 'control-label'>Registered Session:</label>
							{{ HelperController::generateSelectList('AcademicSession','session_name', 'id', 'registered_session_id', $data->registered_session_id) }}
							<span class = 'help-block'>@if($errors->has('registered_session_id')) {{$errors->first('registered_session_id')}} @endif</span>
						</div>
				</div>

				<div class="col-sm-3">
					<div id = "div_for_registered_class_id">				
						<div class = 'form-group @if($errors->has("registered_class_id")) {{"has-error"}} @endif'>
							<label for = 'registered_class_id'  class = 'control-label'>Registered Class:</label>
							
							{{HelperController::generateSelectList('Classes', 
																					

					'class_name', 
																					

					'id', 
																					

					'registered_class_id', 
$data->registered_class_id																		

								
																					

				)}}
							
							
								
							<span class = 'help-block'>@if($errors->has('registered_class_id')) {{$errors->first('registered_class_id')}} 

@endif</span>
						</div>
						
					</div>
				</div>

				<div class="col-sm-3">
					<div id = "div_for_registered_section_code">				
							<div class = 'form-group @if($errors->has("registered_section_code")) {{"has-error"}} @endif'>
								<label for = 'registered_section_code'  class = 'control-label'>Registered Section:</label>
									<select id = "registered_section_code" name = "registered_section_code" class = "form-control">
								<option value = "{{$data->registered_section_code}}">{{$data->registered_section_code}}</option>
							</select>
								<span class = 'help-block'>@if($errors->has('registered_section_code')) {{$errors->first

('registered_section_code')}} @endif</span>
							</div>						
					</div>		
				</div>

			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("guardian_contact")) {{"has-error"}} @endif'>
						<label for = 'guardian_contact'  class = 'control-label'>Guardian Contact :</label>
							
						<input type = 'text' name = 'guardian_contact' value= '{{ $data->guardian_contact }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('guardian_contact')) {{$errors->first('guardian_contact')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
						<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact :</label>
							
						<input type = 'text' name = 'secondary_contact' value= '{{ $data->secondary_contact}}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
						<label for = 'email'  class = 'control-label'>Email :</label>
							
						<input type = 'text' name = 'email' value= '{{ $data->email}}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
							
					</div>
				</div>
			</div>

			<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
				<label for = 'photo'  class = 'control-label'>Photo :</label>
				<br/>
				@if(strlen(trim($data->photo)))
					<img src = "{{Config::get('app.url').'app/modules/student/assets/images/'.$data->photo}}" width="250px" height="auto">
				@else
					<p>No image selected</p>
				@endif
				<input type = "hidden" name = "original_photo" value = "{{$data->photo}}">
				<br/><br/>
				<input type = 'file' name = 'photo'>

				<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>
					
			</div>



			<input type = "hidden" name = "role" value = "student">
			<input type = "hidden" name = "old_session_id" value = "{{$data->registered_session_id}}">
			<input type = "hidden" name = "id" value = "{{$data->id}}">
			<input type = "hidden" name = "old_current_session_id" value = "{{$data->current_session_id}}">
			<input type = "hidden" name = "old_current_class_id" value = "{{$data->current_class_id}}">
			<input type = "hidden" name = "old_current_section_code" value = "{{$data->current_section_code}}">
			<input type = "hidden" name = "unique_school_roll_number" value = "{{$data->unique_school_roll_number}}">
			<input type = 'hidden' name = 'is_active' value = 'yes'>
			{{Form::token()}}
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
			</div>
			
		</form>

</div>
@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
	<script type = "text/javascript">
		function updateClassList(default_session_id, default_class_id) {
			var registered_session_id;
			
			if (typeof(default_session_id) == 'undefined' ) {
				registered_session_id = $('#registered_session_id').val();
			} else {
				registered_session_id = default_session_id;
			}

			$('#registered_class_id').html('<option value="0"> Loading... </option>');

			$.ajax({
        "url": "{{URL::route('student-ajax-active-classes')}}",
        "data": {"session_id" : registered_session_id},
        "method": "GET"
			}).done(function(data) {
 				$('#registered_class_id').html(data);
 				if (typeof(default_class_id) != 'undefined') {
          $('#registered_class_id').val(default_class_id);
        }
			});
		}

		function updateSectionList(default_class_id, default_section_id) {
			var registered_class_id;
			if (typeof default_class_id == 'undefined') {
				registered_class_id = $('#registered_class_id').val();
			} else {
				registered_class_id = default_class_id;
			}
			$('#registered_section_code').html('<option value="0"> Loading... </option>');
			$.ajax( {
        "url": "{{URL::route('student-ajax-active-sections')}}",
        "data": {"class_id" : registered_class_id},
        "method": "GET"
  		}).done(function(data) {
				$('#registered_section_code').html(data);
				if (typeof default_section_id != 'undefined') {
        	$('#registered_section_code').val(default_section_id);
      	}
			});		
		}

		$(function() {
			var ajax_url = $('#ajax_url').val();
			
			updateClassList("{{ $data->registered_session_id }}", "{{ $data->registered_class_id }}");
			updateSectionList("{{ $data->registered_class_id }}", "{{ $data->registered_section_code }}");

			$('#registered_session_id').change(function() {
				updateClassList();
			});
		
			$('#registered_class_id').change(function() {
				updateSectionList();
			});
		});

		function updateCurrentClassList(default_session_id, default_class_id) {
			var current_session_id;
			
			if (typeof(default_session_id) == 'undefined' ) {
				current_session_id = $('#current_session_id').val();
			} else {
				current_session_id = default_session_id;
			}

			$('#current_class_id').html('<option value="0"> Loading... </option>');

			$.ajax({
        "url": "{{URL::route('student-ajax-active-classes')}}",
        "data": {"session_id" : current_session_id},
        "method": "GET"
			}).done(function(data) {
 				$('#current_class_id').html(data);
 				if (typeof(default_class_id) != 'undefined') {
          $('#current_class_id').val(default_class_id);
        }
			});
		}

		function updateCurrentSectionList(default_class_id, default_section_id) {
			var current_class_id;
			if (typeof default_class_id == 'undefined') {
				current_class_id = $('#current_class_id').val();
			} else {
				current_class_id = default_class_id;
			}
			$('#current_section_code').html('<option value="0"> Loading... </option>');
			$.ajax( {
        "url": "{{URL::route('student-ajax-active-sections')}}",
        "data": {"class_id" : current_class_id},
        "method": "GET"
  		}).done(function(data) {
				$('#current_section_code').html(data);
				if (typeof default_section_id != 'undefined') {
        	$('#current_section_code').val(default_section_id);
      	}
			});		
		}

		$(function() {
			var ajax_url = $('#ajax_url').val();
			
			updateCurrentClassList("{{ $data->current_session_id }}", "{{ $data->current_class_id }}");
			updateCurrentSectionList("{{ $data->current_class_id }}", "{{ $data->current_section_code }}");

			$('#current_session_id').change(function() {
				updateCurrentClassList();
			});
		
			$('#current_class_id').change(function() {
				updateCurrentSectionList();
			});
		});
	</script>
	
	<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>


<script src = "{{ asset('sms/assets/js/nepali.datepicker.v2.2.min.js') }}" type = "text/javascript"></script>
<script>
     $(document).ready(function(){
        $('#englishDate9').change(function(){
			$('#nepaliDate9').val(AD2BS($('#englishDate9').val()));
		});

		$('#nepaliDate9').change(function(){
			$('#englishDate9').val(BS2AD($('#nepaliDate9').val()));
		});
	});
</script>

@stop
