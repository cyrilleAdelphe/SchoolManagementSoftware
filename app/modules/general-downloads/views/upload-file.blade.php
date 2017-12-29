{{ Form::open(array('url' => URL::route($module_name . '-file-post'), 'method'=>'POST', 'files'=>true)) }}
	<div class="form_group {{ ($errors->has('fileToUpload')) ? 'has-error' : '' }}">
    <input type="file" name="fileToUpload" id="fileToUpload" />
    <span class = "form-error">
      @if($errors->has('fileToUpload')) 
        {{ $errors->first('fileToUpload') }} 
      @endif
    </span>
  </div>

  <div class="form-group {{ ($errors->has('filename')) ? 'has-error' : ''}}">
    <label for="filename">Save as</label> 
    <input name = "filename" id="filename" class="form-control" type="text" placeholder="Enter file name" 
      value= "{{ (Input::old('filename')) ? (Input::old('filename')) : '' }}">
    <span class = "form-error">
      @if($errors->has('filename')) 
        {{ $errors->first('filename') }} 
      @endif
    </span>
    <small> Leave blank to retain the same filename </small>
  </div>

	<div class = "form-group">
		<button class="btn btn-flat btn-success" type = "submit"> <i class="fa fa-fw fa-upload"></i> Upload </button>
	</div>

	<input type = "hidden" name = "is_active" value = "yes" />
	{{ Form::token() }}
{{ Form::close() }}  