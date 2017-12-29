@extends('testimonial.views.tabs')

@section('tab-content')
             

	    <form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm" enctype = "multipart/form-data">
        <div class = 'form-group @if($errors->has("content")) {{"has-error"}} @endif'>
			<label for = 'content'  class = 'control-label'>Content :</label>
			<textarea class="textarea" name = "content" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('content')) ? (Input::old('content')) : '' }}</textarea><span class = 'help-block'>@if($errors->has('content')) {{$errors->first('content')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("sort_order")) {{"has-error"}} @endif'>
			<label for = 'sort_order'  class = 'control-label'>Sort Order :</label>
			<input type = 'text' name = 'sort_order' value= '{{ (Input::old('sort_order')) ? (Input::old('sort_order')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("profile_pic")) {{"has-error"}} @endif'>
			<label for = 'profile_pic'  class = 'control-label'>Photo :</label>
			<input type = 'file' name = 'profile_pic'><span class = 'help-block'>@if($errors->has('profile_pic')) {{$errors->first('profile_pic')}} @endif</span>
		</div>

		<div class = "form-group">
			<label for = "show_in_module">Show In Module:</label>
			<input type = 'radio' name = 'show_in_module' value = 'yes' @if(Input::old('show_in_module') == 'yes') checked @endif>Yes<input type = 'radio' name = 'show_in_module' value = 'no' @if(Input::old('show_in_module') == 'no') checked @endif>No
		</div>

		<input type = "hidden" name = "is_blocked" value = "0">
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		{{Form::token()}}
		</div>
		<div class="form-group">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>                    
    </form>
     
@stop

