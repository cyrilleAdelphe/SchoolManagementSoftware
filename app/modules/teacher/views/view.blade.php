@extends('backend.'.$current_user->role.'.main')

@section('content')
             
	<div class="tab-pane active" id="tab_1">
	    @if($data)
		    {{$actionButtons}}

		    
		        <div class = 'form-group @if($errors->has("teacher_id")) {{"has-error"}} @endif'>
					<label for = 'name'  class = 'control-label'>Teacher:</label>
					<p>{{$data->employee_name}}</p>
				</div>

				<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
					<label for = 'is_class_teacher'  class = 'control-label'>Is Class Teacher</label>
					<p>{{$data->is_class_teacher}}</p>
				</div>

				<div class = 'form-group @if($errors->has("session_id")) {{"has-error"}} @endif'>
					<label for = 'name'  class = 'control-label'>Session:</label>
					<p>{{$data->session_name}}</p>
				</div>
				

				<div class = 'form-group @if($errors->has("class_id")) {{"has-error"}} @endif'>
					<label for = 'class_id'  class = 'control-label'>Class:</label>
					<p>{{$data->class_name}}</p>						
				</div>
					
				
				<div id = "div_for_section_code">
					
						<div class = 'form-group @if($errors->has("section_code")) {{"has-error"}} @endif'>
							<label for = 'section_code'  class = 'control-label'>Section:</label>
							<p>{{$data->section_code}}</p>
						</div>
					
				</div>

				<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
							<label for = 'section_code'  class = 'control-label'>Is Active:</label>
							<p>{{$data->is_active}}</p>
				</div>
		@else
			<h3>No Record Found</h3>
		@endif
	</div>
     
@stop