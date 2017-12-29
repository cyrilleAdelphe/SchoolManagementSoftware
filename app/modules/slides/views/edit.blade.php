@extends('backend.superadmin.main')

@section('content')
<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
  <form method = "post" action = "{{URL::route($module_name.'-edit-post')}}" id = "backendForm" enctype = "multipart/form-data">


    <div class = 'form-group @if($errors->has("title")) {{"has-error"}} @endif'>
      <label for = 'title'  class = 'control-label'>Slide Title</label>
      <input name = 'title' type = 'text'  value= '{{ (Input::old('title')) ? (Input::old('title')) : $data->title }}' class = 'form-control required' placeholder="Enter unique title"><span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("text")) {{"has-error"}} @endif'>
      <label for = 'text'  class = 'control-label'>Description Text</label>
      <textarea class="textarea" name = "text" placeholder="Enter slide text" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('text')) ? (Input::old('text')) : $data->text }}</textarea><span class = 'help-block'>@if($errors->has('text')) {{$errors->first('text')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("slide_no")) {{"has-error"}} @endif'>
      <label for = 'slide_no'  class = 'control-label'>Slide Number</label>
      <input name = 'slide_no' type = 'text'  value= '{{ (Input::old('slide_no')) ? (Input::old('slide_no')) : $data->slide_no }}' class = 'form-control required' placeholder="Enter unique slide no."><span class = 'help-block'>@if($errors->has('slide_no')) {{$errors->first('slide_no')}} @endif</span>
    </div>

    <div class="form-group @if($errors->has("in_center")) {{"has-error"}} @endif">
      <label for="in_center" class="control-label">Put Text In Center</label>
      <select class="form-control" name="in_center">
        <option value="yes" {{ $data->in_center=='yes' ? 'selected="selected"' : ''}}>Yes</option>
        <option value="no" {{ $data->in_center=='no' ? 'selected="selected"' : ''}}>No</option>
      </select>
    </div>

    <div class = 'form-group @if($errors->has("link")) {{"has-error"}} @endif'>
      <labeel for = 'link'  class = 'control-label'>Link</label>
      <input type = "text" name = "link" placeholder="Enter slide link" class = 'form-control required' value={{ (Input::old('link')) ? (Input::old('link')) : $data->link }}><span class = 'help-block'>@if($errors->has('link')) {{$errors->first('link')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("profile_pic")) {{"has-error"}} @endif'>
        <label for = 'profile_pic'  class = 'control-label'>Upload Picture</label>
        <input type = 'file' name = 'profile_pic'><span class = 'help-block'>@if($errors->has('profile_pic')) {{$errors->first('profile_pic')}} @endif</span>
        <img width="300" height="auto" class="img-responsive" src = "{{Config::get('app.url').'app/modules/slides/asset/images/'.$data->id.'.jpg'}}">
    </div>

    <div class = 'form-group @if($errors->has("slider_class")) {{"has-error"}} @endif'>
      <label for = 'slider_class'  class = 'control-label'>Slider Class</label>
      {{HelperController::generateStaticSelectList(['dark' => 'Dark'], 'slider_class',  (Input::old('slider_class')) ? (Input::old('slider_class')) : $data->slider_class) }}<span class = 'help-block'>@if($errors->has('slider_class')) {{$errors->first('slider_class')}} @endif</span>
    </div>

    <div class="form-group @if($errors->has("is_active")) {{"has-error"}} @endif">
      <label for="is_active" class="control-label">Active</label>
      <select class="form-control" name="is_active">
        <option value="yes" {{ $data->is_active=='yes' ? 'selected="selected"' : ''}}>Yes</option>
        <option value="no" {{ $data->is_active=='no' ? 'selected="selected"' : ''}}>No</option>
      </select>
    </div>
    
    <input type="hidden" name="id" value="{{$data->id}}">
    {{Form::token()}}
   
    <div class="form-group">
        <button class="btn btn-success btn-lg btn-flat" type="submit">Update</button>
    </div>                    
  </form>                              
@stop