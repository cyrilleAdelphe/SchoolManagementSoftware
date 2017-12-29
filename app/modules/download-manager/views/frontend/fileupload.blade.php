@extends('backend.user.account-page')

@section('tab-content')
  
  {{ Form::open(array('url'=>URL::route('download-manager-backend-file-upload-post'),'method'=>'POST', 'files'=>true)) }}
    <div class = "form_group">
      <h2>Single file upload</h2>
    </div>

    <div class="form_group">
        
        <input type="file" name="fileToUpload" id="fileToUpload"  />
        <span class = "form-error">
          @if($errors->has('fileToUpload')) 
            {{ $errors->first('fileToUpload') }} 
          @endif
        </span>
    </div>

    <div class="form-group">
      <label for="filename">Save as</label> 
      <input name = "filename" id="filename" class="form-control" type="text" placeholder="Enter file name" 
        value= "{{ (Input::old('filename')) ? (Input::old('filename')) : '' }}">
      <span class = "form-error">
        @if($errors->has('filename')) 
          {{ $errors->first('filename') }} 
        @endif
      </span>
    </div>

    <div class="form-group">
      <label for="tags">Tags</label> 
      <input name = "tags" id="tags" class="form-control" type="text" placeholder="Enter relevant keywords" 
        value= "{{ (Input::old('tags')) ? (Input::old('tags')) : '' }}">
      <span class = "form-error">
        @if($errors->has('tags')) 
          {{ $errors->first('tags') }} 
        @endif
      </span>
    </div>

    <div class="form-group">
      <label for="content">Description</label>
      <textarea class="textarea" name="description" placeholder="Describe your file" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('description')) ? (Input::old('description')) : '' }}</textarea>

      <span class = "form-error">
        @if($errors->has('description')) 
          {{ $errors->first('description') }} 
        @endif
      </span>
    </div>

    <input type="hidden" name="is_active" value="no"/>
    <input type="hidden" name="is_featured" value="no"/>
    <input type="hidden" name="is_frontend" value="yes"/>

    <div class="form-group">
      <button class="btn btn-primary" type="submit">Upload</button>
    </div>

    {{ Form::token() }}

  {{ Form::close() }}  

  @include('download-manager.views.frontend.userfiles')
  
@stop


