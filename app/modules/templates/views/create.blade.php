@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

<div class = 'container'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
	
		<div class = 'form-group @if($errors->has("template_name")) {{"has-error"}} @endif'>
			<label for = 'template_name'  class = 'control-label col-xs-2'>Template Name :</label>
				<div class = 'col-xs-10'>
			<input type = 'text' name = 'template_name' value= '{{ (Input::old('template_name')) ? (Input::old('template_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('template_name')) {{$errors->first('template_name')}} @endif</span>
				</div>
		</div>
		
		<div class = 'form-group @if($errors->has("template_alias")) {{"has-error"}} @endif'>
			<label for = 'template_alias'  class = 'control-label col-xs-2'>Template Alias :</label>
				<div class = 'col-xs-10'>
			<input type = 'text' name = 'template_alias' value= '{{ (Input::old('template_alias')) ? (Input::old('template_alias')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('template_alias')) {{$errors->first('template_alias')}} @endif</span>
				</div>
		</div>

		<!-- get all positions here -->
		<div class = 'form-group'>
			<label for = 'position_id'  class = 'control-label col-xs-2'>Position :</label>
				<div class = 'col-xs-10'>
				@foreach($positions as $p)
					<div>
						<input type = "checkbox" name = "position_id[]" value = "{{$p->id}}" class = "position_id">{{$p->position_name}}<label>Sort Order: </label><input type = "text" name = "sort_order[]" class = "eton_sort_order_disabled" disabled>
					</div>
				@endforeach
				</div>
		</div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>
	</form>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
{{File::get(app_path().'/modules/templates/assets/js/create-edit.js')}}
@stop
