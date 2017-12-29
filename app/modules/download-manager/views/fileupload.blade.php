@extends('download-manager.views.main')

@section('tabs')
	<div class="tab-content">
	    <div class="tab-pane active" id="tab_active">
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

		      <input type="hidden" name="is_active" value="yes"/>
					
					<div class = "form-group">
				      <label for = "is_featured">Is Featured:</label>
				      <input type = "radio" name = "is_featured" value = "yes" @if(Input::old('is_featured') == 'yes') checked @endif>Yes<input type = "radio" name = "is_featured" value = "no" @if(Input::old('is_featured') == 'no' || !Input::has('is_featured')) checked @endif>No
				  </div>

		      <div class="form-group">
		        <button class="btn btn-primary" type="submit">Upload</button>
		      </div>

		      {{ Form::token() }}

		    {{ Form::close() }}  
		   

		    <br/><br/>

		    {{ Form::open(array('url'=>URL::route('download-manager-backend-files-upload-post'),'method'=>'POST', 'files'=>true)) }}
		        <div class = "form_group">
		          <h2>Mutiple file upload</h2>
		        </div>
		        <div class="form_group">
		          <input type="file" name="filesToUpload[]" id="filesToUpload"  multiple="multiple"/>
		          {{-- Form::file('filesToUpload') --}}


		          <span class = "form-error">
		            @if($errors->has('filesToUpload')) 
		              {{ $errors->first('filesToUpload') }} 
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
		          <textarea class="textarea" name="description" placeholder="Describe your files" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
		              {{ (Input::old('description')) ? (Input::old('description')) : '' }}
		          </textarea>

		          <span class = "form-error">
		            @if($errors->has('description')) 
		              {{ $errors->first('description') }} 
		            @endif
		          </span>
		        </div>

		        <input type="hidden" name="is_active" value="yes"/>
					
						<div class = "form-group">
					      <label for = "is_featured">Is Featured:</label>
					      <input type = "radio" name = "is_featured" value = "yes" @if(Input::old('is_featured') == 'yes') checked @endif>Yes<input type = "radio" name = "is_featured" value = "no" @if(Input::old('is_featured') == 'no' || !Input::has('is_featured')) checked @endif>No
					  </div>
		        
		        <div class="form-row">
		          <button class="btn btn-primary" type="submit">Upload</button>
		        </div>
		        {{ Form::token() }}
		      {{ Form::close() }}
	    </div>
	</div>
@stop

@section('download-manager-scripts')
	
	<script type="text/javascript">
    	document.getElementById("file_upload_tab").setAttribute("class", "active");
    </script>
    
    
@stop