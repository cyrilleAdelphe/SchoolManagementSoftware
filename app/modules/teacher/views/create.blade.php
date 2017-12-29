@extends('include.form-tabs')

@section('tab-content')
             
	<div class="tab-pane active" id="tab_1">
	    {{$actionButtons}}

	    <form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm" enctype = "multipart/form-data">
	        <div class = 'form-group @if($errors->has("teacher_id")) {{"has-error"}} @endif'>
				<label for = 'name'  class = 'control-label'>Teacher:</label>
				{{HelperController::generateStaticSelectList($teachers, 'teacher_id', Input::old('teacher_id'))}}
				<span class = 'help-block'>@if($errors->has('teacher_id')) {{$errors->first('teacher_id')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
				<label for = 'is_class_teacher'  class = 'control-label'>Is Class Teacher</label>
				<input type = 'radio' name = 'is_class_teacher' value = 'yes' @if(Input::old('is_class_teacher') == 'yes') {{'checked'}} @endif>Yes<input type = 'radio' name = 'is_class_teacher' value = 'no' @if(Input::old('is_class_teacher') == 'no') {{'checked'}} @endif>No
				<span class = 'help-block'>@if($errors->has('is_class_teacher')) {{$errors->first('is_class_teacher')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
				<label for = 'name'  class = 'control-label'>Session:</label>
				{{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', Input::old('session_id', HelperController::getCurrentSession()))}}
				<span class = 'help-block'>@if($errors->has('session_id')) {{$errors->first('session_id')}} @endif</span>
			</div>
			

			<div id = "div_for_class_id">
				
				<div class = 'form-group @if($errors->has("class_id")) {{"has-error"}} @endif'>
					<label for = 'class_id'  class = 'control-label'>Class:</label>
						<select id = "class_id" name = "class_id" class = "form-control">
							<option>Please select session first</option>
						</select>
					<span class = 'help-block'>@if($errors->has('class_id')) {{$errors->first('class_id')}} @endif</span>
				</div>
				
			</div>

			<div id = "div_for_section_code">
				
					<div class = 'form-group @if($errors->has("section_code")) {{"has-error"}} @endif'>
						<label for = 'section_code'  class = 'control-label'>Section:</label>
							<select id = "section_code" name = "section_code" class = "form-control">
								<option>Please select class first</option>
							</select>
						<span class = 'help-block'>@if($errors->has('section_code')) {{$errors->first('section_code')}} @endif</span>
					</div>
				
			</div>

			<input type = 'hidden' name = 'is_active' value = 'yes'>
			{{Form::token()}}
			<input type = "hidden" id = "ajax_url" value = "{{URL::route('ajax-active-classes')}}">
			</div>
			<div class="form-group">
	            <button class="btn btn-primary" type="submit">Submit</button>
	        </div>                    
	    </form>                              
	</div>
     
@stop

@section('custom-js')
	<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
	<script>
		function updateClass() {
			var session_id = $('#session_id').val();
			if (session_id == 0) return;
			$.ajax({
					            "url": "{{URL::route('ajax-active-classes')}}",
					            "data": {"session_id" : session_id},
					            "method": "GET"
		          			}).done(function(data) {
					 				
					 				$('#class_id').html(data);
					});
		}
	</script>
	<script type = "text/javascript">
		$(function()
		{
			var ajax_url = $('#ajax_url').val();

			$('#session_id').change(function()
			{
				updateClass();
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
			// update class_id according to default academic session
			updateClass();
		});
	</script>
@stop
