@extends('assignments.views.tabs')

@section('tab-content')

<div class="row">

	<div class="col-sm-3">
    <div class="form-group">
      <label>Select Session</label>
      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', $selected = Input::old('academic_session_id') ? Input::old('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
      <span class = "form-error">
        @if($errors->has('academic_session_id')) 
          {{ $errors->first('academic_session_id') }} 
        @endif
      </span>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
      <label>Select class</label>
      <select name="class_id" id="class_id" class="form-control">
				<option value="0">--Select Session First--</option>
			</select>
			<span class = "form-error">
        @if($errors->has('class_id')) 
          {{ $errors->first('class_id') }} 
        @endif
      </span>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group @if($errors->has("section_id")) {{"has-error"}} @endif">
      <label>Select section</label>
      <select name="section_id" id="section_id" class="form-control">
				<option value="0">--Select Class First--</option>
			</select>
			<span class = "form-error">
        @if($errors->has('section_id')) 
          {{ $errors->first('section_id') }} 
        @endif
      </span>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group @if($errors->has("subject_id")) {{"has-error"}} @endif">
      <label>Select subject</label>
      <select name="subject_id" id="subject_id" class="form-control">
				<option value="0">--Select Class/Section First--</option>
			</select>
			<span class = "form-error">
        @if($errors->has('subject_id')) 
          {{ $errors->first('subject_id') }} 
        @endif
      </span>
    </div>
  </div>
</div><!-- row ends -->
  {{ Form::open(array('url'=>URL::route('assignments-upload-file-post'),'method'=>'POST', 'files'=>true)) }}

    <h4 class="text-red">Single file upload</h4>

    <div class="form_group @if($errors->has("fileToUpload")) {{"has-error"}} @endif">
      <input type="file" name="fileToUpload" id="fileToUpload" />
      <span class = "form-error">
        @if($errors->has('fileToUpload')) 
          {{ $errors->first('fileToUpload') }} 
        @endif
      </span>
    </div>

    <div class="form-group @if($errors->has("filename")) {{"has-error"}} @endif">
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
      <textarea class="textarea" name="description" placeholder="Describe your file" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('description')) ? (Input::old('description')) : '' }}</textarea>

      <span class = "form-error">
        @if($errors->has('description')) 
          {{ $errors->first('description') }} 
        @endif
      </span>
    </div>

    <input type="hidden" name="is_active" value="yes"/>
    <input type="hidden" name="is_featured" value="no"/>

    <input type="hidden" name="academic_session_id" id="single_upload_academic_session_id" value=""/>
    <input type="hidden" name="class_id" id="single_upload_class_id" value=""/>
    <input type="hidden" name="section_id" id="single_upload_section_id" value=""/>
    <input type="hidden" name="subject_id" id="single_upload_subject_id" value=""/>
		
		<div class="form-group">
      <button id= "single_upload_button" class="btn btn-success btn-lg btn-flat" type="submit"><i class="fa fa-fw fa-upload"></i> Upload</button>
    </div>

    {{ Form::token() }}

  {{ Form::close() }}  
 

  <br/>

  {{ Form::open(array('url'=>URL::route('assignments-upload-files-post'),'method'=>'POST', 'files'=>true)) }}
      <h4 class="text-red">Mutiple file upload</h4>
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
        <textarea class="textarea" name="description" placeholder="Describe your files" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
            {{ (Input::old('description')) ? (Input::old('description')) : '' }}
        </textarea>

        <span class = "form-error">
          @if($errors->has('description')) 
            {{ $errors->first('description') }} 
          @endif
        </span>
      </div>

      <input type="hidden" name="is_active" value="yes"/>
			<input type="hidden" name="is_featured" value="no"/>

			<input type="hidden" name="academic_session_id" id="multiple_upload_academic_session_id" value=""/>
    <input type="hidden" name="class_id" id="multiple_upload_class_id" value=""/>
    <input type="hidden" name="section_id" id="multiple_upload_section_id" value=""/>
    <input type="hidden" name="subject_id" id="multiple_upload_subject_id" value=""/>
			      
      <div class="form-row">
        <button id="multiple_upload_button" class="btn btn-success btn-lg btn-flat" type="submit"><i class="fa fa-fw fa-upload"></i> Upload</button>
      </div>
      {{ Form::token() }}
    {{ Form::close() }}

</div><!-- row ends -->

<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />

<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />

<input type="hidden" id="subject_ajax" value="{{URL::route('ajax-subject-get-subjects')}}" />

<input type="hidden" name= "default_academic_session" id="default_academic_session" value="{{Input::old('academic_session_id')?Input::old('academic_session_id'):''}}" />

<input type="hidden" name= "default_class" id="default_class" value="{{Input::old('class_id')?Input::old('class_id'):''}}" />

<input type="hidden" name= "default_section" id="default_section" value="{{Input::old('section_id')?Input::old('section_id'):''}}" />

<input type="hidden" name= "default_subject" id="default_subject" value="{{Input::old('subject_id')?Input::old('subject_id'):''}}" />


@stop

@section('custom-js')

<script src="{{ asset('backend-js/validation.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSubjectList.js') }}" type="text/javascript"></script>


<script>
	$(document).on('change', '#academic_session_id', updateClassList);
	$(document).on('change', '#class_id', updateSectionList);
	$(document).on('change', '#section_id', updateSubjectList);
	$(document).on('click', '#single_upload_button', function(){
		$('#single_upload_academic_session_id').val($('#academic_session_id').val());
		$('#single_upload_class_id').val($('#class_id').val());
		$('#single_upload_section_id').val($('#section_id').val());
		$('#single_upload_subject_id').val($('#subject_id').val());
	});

	$(document).on('click', '#multiple_upload_button', function(){
		$('#multiple_upload_academic_session_id').val($('#academic_session_id').val());
		$('#multiple_upload_class_id').val($('#class_id').val());
		$('#multiple_upload_section_id').val($('#section_id').val());
		$('#multiple_upload_subject_id').val($('#subject_id').val());
	});
  
  $(function(){
	  if($('#academic_session_id').val() != 0)
		{
			updateClassList($('#default_class').val());
			updateSectionList($('#default_section').val());
			updateSubjectList($('#default_subject').val());
		}
	});
</script>


@stop