@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>
	  Library Manager
	  <small>Create or edit books category</small>
	</h1>
@stop

@section('content')
	<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}" id = "backendForm">
    <div class="form-group @if($errors->has('title')) {{'has-error'}} @endif">
      <label for="title">Category Title</label>
      <input id="title" name="title" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title')?Input::old('title'):$data->title}}" required>
      <span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('from_class')) {{'has-error'}} @endif">
      <label for="range">From Class Range </label>
      <input data-toggle="tooltip" id="to" class="form-control" type="text" placeholder="Enter class range (eg. 5-8)" name="from_class" value="{{Input::old('from_class')?Input::old('from_class'):$data->from_class}}">
      <small class='text-danger'>You can write this field 0 if the category is universal.</small>
      <span class = 'help-block'>@if($errors->has('from_class')) {{$errors->first('from_class')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('to_class')) {{'has-error'}} @endif">
      <label for="range">To Class Range </label>
      <input data-toggle="tooltip" id="to" class="form-control" type="text" placeholder="Enter class range (eg. 5-8)" name="to_class" value="{{Input::old('to_class')?Input::old('to_class'):$data->to_class}}">
      <small class='text-danger'>You can write this field 0 if the category is universal.</small>
      <span class = 'help-block'>@if($errors->has('to_class')) {{$errors->first('to_class')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has('description')) {{'has-error'}} @endif">
      <label for="content">Category Description</label>
      <textarea class="textarea" name="description" placeholder="Describe about category here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{Input::old('description')?Input::old('description'):$data->description}}</textarea>
      <span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
    </div>
    <div class="form-group">
      <button class="btn btn-primary" type="submit">Submit</button>
    </div>
    <input type="hidden" name="id" value="{{$data->id}}"/>
    <input type="hidden" name="is_active" value="yes"/>
    {{Form::token()}}
  </form>
@stop

@section('custom-js')
	<script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script type="text/javascript">
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();
    });
  </script>
@stop