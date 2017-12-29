@extends('backend.' . $role . '.main')

@section('content')
	@if (!isset($auth_url))
		{{ Form::open(array('url'=>URL::route('download-manager-drive-config-post'), 'method'=>'POST', 'files'=>true, 'id'=>'clientSecretForm')) }}
			<div class="form_group">
				<label> Client Secret (json file): </label>
			  <input type="file" name="client_secret" />
			</div>
			<div class="form_group">
				<label> Application Name: </label>
				<input type="text" name="application_name" />
			</div>
			<div class="form-group">
	      <button class="btn btn-primary" id="submit">Upload</button>
	    </div>
	    {{ Form::token() }}
	  {{ Form::close() }}
	@else 
		Open the following link and enter the validation code provided <br /> 
		<a href="{{ $auth_url }}" id="auth_url"> Verification link </a>
		{{ Form::open(array('url'=>'#', 'method'=>'POST', 'files'=>true, 'id'=>'clientSecretForm')) }}
			<div class="form_group">
				<label> Validation Code: </label>
				<input type="text" name="validation_code" />
			</div>
			<input type="hidden" name="application_name" value="{{ $application_name }}" />
			<div class="form-group">
	      <button class="btn btn-primary" id="submit">Upload</button>
	    </div>
		{{ Form::close() }}
	@endif
@endsection

@section('custom-js')
	<script type="text/javascript">
  	$(function() {
  		$('#auth_url').click(function(e) {
  			e.preventDefault();
  			window.open($(this).attr('href'), '_blank');
  		});
  	});
  </script>
@endsection
