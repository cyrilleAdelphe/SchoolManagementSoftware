@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>
	  Library Manager
	  <small>Create or edit books category</small>
	</h1>
@stop

@section('content')
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm">
    <div class="form-group @if($errors->has('title')) {{'has-error'}} @endif">
      <label for="title">Category Title</label>
      <input id="title" name="title" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title')?Input::old('title'):''}}" required>
      <span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
    </div>
   {{-- <div class="form-group @if($errors->has('class_range')) {{'has-error'}} @endif">
      <label for="range">Class Range </label>
      <input data-toggle="tooltip" title="You can enter multiple classes by seperating with , sign and define range with - sign." id="to" class="form-control" type="text" placeholder="Enter class range (eg. 5-8)" name="class_range" value="{{Input::old('class_range')?Input::old('class_range'):''}}">
      <small class='text-danger'>You can leave this field blank if the category is universal.</small>
      <span class = 'help-block'>@if($errors->has('class_range')) {{$errors->first('class_range')}} @endif</span>
    </div> --}}
     <div class="form-group @if($errors->has('rack_number')) {{'has-error'}} @endif">
      <label for="rack_number">Rack Number </label>
      <input id="rack_number" name="rack_number" class="form-control" type="text" placeholder="Enter rack_number" value="{{Input::old('rack_number')?Input::old('rack_number'):''}}" required>
      <span class = 'help-block'>@if($errors->has('rack_number')) {{$errors->first('rack_number')}} @endif</span>
    </div>
    
    <div class="form-group @if($errors->has('description')) {{'has-error'}} @endif">
      <label for="content">Category Description</label>
      <textarea class="textarea" name="description" placeholder="Describe about category here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{Input::old('description')?Input::old('description'):''}}</textarea>
      <span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
    </div>
    <div class="form-group">
      <button class="btn btn-primary submit-enable-disable" type="submit" related-form='backendForm'>Submit</button>
    </div>
    <input type="hidden" name="is_active" value="yes"/>
    {{Form::token()}}
  </form>
@stop

@section('custom-js')
	<script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
	<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
  <script type="text/javascript">
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();
    });
  </script>
@stop