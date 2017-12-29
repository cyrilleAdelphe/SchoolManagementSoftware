@extends('backend.'.$current_user->role.'.main')

@section('content')
             
	<div class="tab-pane active" id="tab_1">
	    @if($data)
		    {{$actionButtons}}

		    <form method = "post" action = "{{URL::route($module_name.'-edit-post')}}" id = "backendForm" enctype = "multipart/form-data">
		        <div class = 'form-group @if($errors->has("teacher_id")) {{"has-error"}} @endif'>
					<label for = 'name'  class = 'control-label'>Teacher:</label>
					{{HelperController::generateStaticSelectList($teachers, 'teacher_id', $data->teacher_id)}}
					<span class = 'help-block'>@if($errors->has('teacher_id')) {{$errors->first('teacher_id')}} @endif</span>
				</div>

				<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
					<label for = 'is_class_teacher'  class = 'control-label'>Is Class Teacher</label>
					<input type = 'radio' name = 'is_class_teacher' value = 'yes' @if($data->is_class_teacher == 'yes') {{'checked'}} @endif>Yes<input type = 'radio' name = 'is_class_teacher' value = 'no' @if($data->is_class_teacher == 'no') {{'checked'}} @endif>No
					<span class = 'help-block'>@if($errors->has('is_class_teacher')) {{$errors->first('is_class_teacher')}} @endif</span>
				</div>

				<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
					<label for = 'name'  class = 'control-label'>Session:</label>
					{{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', $data->session_id)}}
					<span class = 'help-block'>@if($errors->has('session_id')) {{$errors->first('session_id')}} @endif</span>
				</div>
				

				<div id = "div_for_class_id">
					
					<div class = 'form-group @if($errors->has("class_id")) {{"has-error"}} @endif'>
						<label for = 'class_id'  class = 'control-label'>Class:</label>
							<select id = "class_id" name = "class_id" class = "form-control">
								<option value = "{{$data->class_id}}">{{$data->class_name}}</option>
							</select>
						<span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
					</div>
					
				</div>

				<div id = "div_for_section_code">
					
						<div class = 'form-group @if($errors->has("section_code")) {{"has-error"}} @endif'>
							<label for = 'section_code'  class = 'control-label'>Section:</label>
								<select id = "section_code" name = "section_code" class = "form-control">
									<option value = "{{$data->section_code}}">{{$data->section_code}}</option>
								</select>
							<span class = 'help-block'>@if($errors->has('section_code')) {{$errors->first('section_code')}} @endif</span>
						</div>
					
				</div>

				<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
							<label for = 'section_code'  class = 'control-label'>Is Active:</label>
							<input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>Yes
							<input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>No
				</div>

				
				{{Form::token()}}

				</div>
				<div class="form-group">
					<input type = "hidden" name = "id" value = "{{$data->id}}">
		            <button class="btn btn-primary" type="submit">Submit</button>
		        </div>                    
		    </form> 
		@else
			<h3>No Record found</h3>
		@endif                             
	</div>
     
@stop

@section('custom-js')
	<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
	<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
	<script type = "text/javascript">
		$(function()
		{
			var ajax_url = $('#ajax_url').val();

			$('#session_id').change(function()
			{
				
				var session_id = $(this).val();
				$.ajax({
						            "url": "{{URL::route('ajax-active-classes')}}",
						            "data": {"session_id" : session_id},
						            "method": "GET"
			          			}).done(function(data) {
						 				
						 				$('#class_id').html(data);
						});
			});
		
			$('#class_id').change(function()
			{

				var class_id = $(this).val();
			
				$.ajax( {
					            "url": "{{URL::route('ajax-active-sections')}}",
					            "data": {"class_id" : class_id},
					            "method": "GET"
			          			} ).done(function(data) {
									$('#section_code').html(data);
								});		
			});

		});
	</script>
@stop
