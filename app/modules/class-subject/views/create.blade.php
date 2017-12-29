@extends('include.form-tabs')

@section('tab-content')

{{$actionButtons}}

<div class = 'container'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
		<div class = "container">
			@foreach($data['classess'] as $class_id => $c)
				<div class = 'form-group'>
					<h2>{{$c}}</h2>
					<input type = "hidden" name = "class_id[]" value = "{{$class_id}}">
					<div>
						@foreach($data['subjects'] as $s)
							<span><input type = "checkbox" name = "subjects_of_class_{{$class_id}}[]" value = "{{$s}}" @if(isset($data['classessSubjects'][$class_id]) && in_array($s, $data['classessSubjects'][$class_id])) checked @endif>{{$s}}</span>
						@endforeach
					</div>
				</div>
			@endforeach
			<input type = 'hidden' name = 'is_active' value = 'yes'>
			<div class = 'form-row'>
				<div class='col-xs-offset-2 col-xs-10'>
				{{Form::token()}}
				</div>
			</div>
		</div>
	</form>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

@stop
