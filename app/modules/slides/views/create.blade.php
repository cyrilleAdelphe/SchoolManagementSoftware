@extends('include.form-tabs')

@section('tab-content')
<?php 
$now = new DateTime();
?>
  <form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm" enctype = "multipart/form-data">

    <div class = 'form-group @if($errors->has("title")) {{"has-error"}} @endif'>
      <label for = 'title'  class = 'control-label'>Slide Title</label>
      <input name = 'title' type = 'text'  value= '{{ (Input::old('title')) ? (Input::old('title')) : '' }}' class = 'form-control required' placeholder="Enter unique title"><span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("text")) {{"has-error"}} @endif'>
      <label for = 'text'  class = 'control-label'>Description Text</label>
      <textarea class="textarea" name = "text" placeholder="Enter slide text" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('text')) ? (Input::old('text')) : '' }}</textarea><span class = 'help-block'>@if($errors->has('text')) {{$errors->first('text')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("slide_no")) {{"has-error"}} @endif'>
      <label for = 'slide_no'  class = 'control-label'>Slide Number</label>
      <input name = 'slide_no' type = 'text'  value= '{{ (Input::old('slide_no')) ? (Input::old('slide_no')) : '' }}' class = 'form-control required' placeholder="Enter unique slide no."><span class = 'help-block'>@if($errors->has('slide_no')) {{$errors->first('slide_no')}} @endif</span>
    </div>

    <div class="form-group @if($errors->has("in_center")) {{"has-error"}} @endif">
      <label for="in_center" class="control-label">Put Text In Center</label>
      <select class="form-control" name="in_center">
        <option value="yes" {{ Input::old('in_center') =='yes' ? 'selected="selected"' : ''}}>Yes</option>
        <option value="no" {{ Input::old('in_center') =='no' ? 'selected="selected"' : ''}}>No</option>
      </select>
    </div>


    <div class = 'form-group @if($errors->has("slider_class")) {{"has-error"}} @endif'>
      <label for = 'slider_class'  class = 'control-label'>Slider Class</label>
      {{HelperController::generateStaticSelectList(['dark' => 'Dark'], 'slider_class',  (Input::old('slider_class')) ? (Input::old('slider_class')) : '') }}<span class = 'help-block'>@if($errors->has('slider_class')) {{$errors->first('slider_class')}} @endif</span>
    </div>

    
    <div class = 'form-group @if($errors->has("link")) {{"has-error"}} @endif'>
      <label for = 'link'  class = 'control-label'>Link</label>
      <input name = 'link' type = 'text'  value= '{{ (Input::old('link')) ? (Input::old('link')) : '' }}' class = 'form-control required' placeholder="Enter unique link"><span class = 'help-block'>@if($errors->has('link')) {{$errors->first('link')}} @endif</span>
    </div>


    <div class = 'form-group @if($errors->has("profile_pic")) {{"has-error"}} @endif'>
        <label for = 'profile_pic'  class = 'control-label'>Upload Picture</label>
        <input type = 'file' name = 'profile_pic'><span class = 'help-block'>@if($errors->has('profile_pic')) {{$errors->first('profile_pic')}} @endif</span>
    </div>

    <input type = "hidden" name = "is_blocked" value = "0">
    <input type = 'hidden' name = 'is_active' value = 'yes'>
    {{Form::token()}}
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-lg btn-flat" type="submit">Submit</button>
    </div>                    
  </form>                              
@stop