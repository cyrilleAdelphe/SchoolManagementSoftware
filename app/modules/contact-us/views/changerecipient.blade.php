@extends('backend.'.$role.'.contact_us_manager')

@section('tab-content')
	<form method = "post" action = "{{URL::route('contact-us-config-post')}}">
		<div class="form-group @if($errors->has("recipient_email")) {{"has-error"}} @endif">
			<label for="email">Recipient email</label> 
			<input name = "recipient_email" id="recipient_email" class="form-control email required" type="text" placeholder="Enter number of articles" 
				value= "{{ (Input::old('recipient_email')) ? (Input::old('recipient_email')) :
							 ( isset($config) ? $config['recipient_email'] : '') }}">
			<span class = "form-error">
				@if($errors->has('recipient_email')) 
					{{ $errors->first('recipient_email') }} 
				@endif
			</span>
		</div>

		<div class="form-group">
			<button class="btn btn-success btn-lg btn-flat" type="submit">Update</button>
		</div>
		{{ Form::token() }}
	</form>
@stop

@section('custom-js')
	<script src = "{{asset('public/backend-js/actionButtons.js') }}" type = "text/javascript"></script>
	<script src = "{{asset('public/backend-js/validation.js') }}" type = "text/javascript"></script>
@stop