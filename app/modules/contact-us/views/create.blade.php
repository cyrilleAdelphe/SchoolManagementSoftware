@extends('frontend.main')


@section('content')
 <div class="container">            
	<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
        	<section class="content">
          		<div class="box">
            		<div class="box-body">
            			<h1>Contact Us</h1>
					    <form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm">
					        
					        <div class = 'form-group @if($errors->has("sender_name")) {{"has-error"}} @endif'>
								<label for = 'sender_name'  class = 'control-label'>Name :</label>
								<input type = 'text' name = 'sender_name' 
										value= '{{ (Input::old('sender_name')) ? (Input::old('sender_name')) : '' }}' 
										class = 'form-control required'>
								<span class = 'help-block'>
									@if($errors->has('sender_name')) {{$errors->first('sender_name')}} @endif
								</span>
							</div>

					        <div class = 'form-group @if($errors->has("sender_email")) {{"has-error"}} @endif'>
								<label for = 'email'  class = 'control-label'>Email :</label>
								<input type = 'text' name = 'sender_email' id = "email"
										value= '{{ (Input::old('sender_email')) ? (Input::old('sender_email')) : '' }}' 
										class = 'form-control required'>
								<span class = 'help-block'>
									@if($errors->has('sender_email')) {{$errors->first('sender_email')}} @endif
								</span>
							</div>


							<div class = 'form-group @if($errors->has("sender_location")) {{"has-error"}} @endif'>
								<label for = 'sender_location'  class = 'control-label'>Address :</label>
								<input type = 'text' name = 'sender_location' 
										value= '{{ (Input::old('sender_location')) ? (Input::old('sender_location')) : '' }}' 
										class = 'form-control required'>
								<span class = 'help-block'>
									@if($errors->has('sender_location')) {{$errors->first('sender_location')}} @endif
								</span>
							</div>

							<div class = 'form-group @if($errors->has("subject")) {{"has-error"}} @endif'>
								<label for = 'subject'  class = 'control-label'>Subject :</label>
								<input type = 'text' name = 'subject' 
										value= '{{ (Input::old('subject')) ? (Input::old('subject')) : '' }}' 
										class = 'form-control required'>
								<span class = 'help-block'>
									@if($errors->has('subject')) {{$errors->first('subject')}} @endif
								</span>
							</div>

							<div class = 'form-group @if($errors->has("query")) {{"has-error"}} @endif'>
								<label for = 'query'  class = 'control-label'>Query :</label>
								<textarea class="textarea" name = "query" placeholder="Place you query here" 
											style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('query')) ? (Input::old('query')) : '' }}</textarea>
								<span class = 'help-block'>
									@if($errors->has('query')) {{$errors->first('query')}} @endif
								</span>
							</div>

							<input type = 'hidden' name = 'is_active' value = 'yes'>
							{{Form::token()}}
							</div>
							<div class="form-group">
					            <button class="btn btn-primary" type="submit">Submit</button>
					        </div>                    
					    </form>     
					</div>
				</div>
			</section>
		</section>
	</div>
</div>
     
@stop

@section('custom-js')
	<script src = "{{asset('public/backend-js/actionButtons.js') }}" type = "text/javascript"></script>
	<script src = "{{asset('public/backend-js/validation.js') }}" type = "text/javascript"></script>
@stop
