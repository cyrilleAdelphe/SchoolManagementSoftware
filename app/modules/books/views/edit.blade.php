@extends('backend.'.$role.'.main')

@section('custom-css')
  
@stop

@section('page-header')
    <h1>
      Books Entry
      <small>Create or edit books</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Library Manager</a></li>
      <li class="active">Books Entry</li>
    </ol>
@stop

@section('content')
    <form method="post" action="{{URL::route($module_name.'-edit-post',$data->id)}}"  id = "backendForm" enctype = "multipart/form-data">
    <div class="form-group @if($errors->has("title")) {{"has-error"}} @endif">
      <label for="title">Book Title</label>
      <input id="title" name="title" class="form-control" type="text" placeholder="Enter title" value="{{Input::old('title')?Input::old('title'):$data->title}}">
      <span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has("category_id")) {{"has-error"}} @endif">
      <label>Choose Category</label>
      {{HelperController::generateStaticSelectList($categories, 'category_id', Input::old('category_id')?Input::old('category_id'):$data->category_id)}}
      <span class = 'help-block'>@if($errors->has('category_id')) {{$errors->first('category_id')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has("author")) {{"has-error"}} @endif">
      <label for="author">Author</label>
      <input id="author" name="author" class="form-control" type="text" placeholder="Written by" value="{{Input::old('author')?Input::old('author'):$data->author}}">
      <span class = 'help-block'>@if($errors->has('author')) {{$errors->first('author')}} @endif</span>
    </div>

    <div class="form-group @if($errors->has("published_date")) {{"has-error"}} @endif">
      <label>Published date</label>
      <div class="input-group">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" name="published_date" class="form-control" placeholder='Enter published date' data-inputmask="'alias': 'yyyy-mm-dd'" data-mask value="{{Input::old('published_date')?Input::old('published_date'):$data->published_date}}"/>
        <span class = 'help-block'>@if($errors->has('published_date')) {{$errors->first('published_date')}} @endif</span>
      </div>
    </div>
    <div class="form-group @if($errors->has("price")) {{"has-error"}} @endif">
      <label for="price">Price</label>
      <input id="price" name="price" class="form-control" type="text" placeholder="Enter book price" value="{{Input::old('price')?Input::old('price'):$data->price}}">
      <span class = 'help-block'>@if($errors->has('price')) {{$errors->first('price')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has("book_ids")) {{"has-error"}} @endif">
      <label for="range">Unique ID </label>
      <input name="book_ids" data-toggle="tooltip" title="You can enter multiple ID by seperating with , sign or define range with - sign." id="range" class="form-control" type="text" placeholder="Enter book number/range" value="{{Input::old('book_ids')?Input::old('book_ids'):$book_ids}}">
      <small class='text-danger'>You can leave this field blank if you dont want to give any unique IDs for the books</small>
      <span class = 'help-block'>
        {{-- The two errors book_ids and non_unique_book_ids are mutex --}}
        @if($errors->has('book_ids')) 
          {{ $errors->first('book_ids') }} 
        @endif

        {{-- Display already registered book ids --}}
        @if($errors->has('non_unique_book_ids'))
          {{ $errors->first('non_unique_book_ids') }}
        @endif
    </span>    
      </span>
    </div>
    <div class="form-group @if($errors->has("max_holding_days")) {{"has-error"}} @endif">
      <label for="total">Maximum holding days</label>
      <input id="total" name="max_holding_days" class="form-control" type="text" placeholder="Enter numnber of days" value="{{Input::old('max_holding_days')?Input::old('max_holding_days'):$data->max_holding_days}}">
      <span class = 'help-block'>@if($errors->has('max_holding_days')) {{$errors->first('max_holding_days')}} @endif</span>
    </div>
    <div class="form-group @if($errors->has("image")) {{"has-error"}} @endif">
      <label for="profilePic">Upload New Picture</label>
      <input id="profilePic" name="image" type="file">
      <p class="help-block">You can upload the picture of book here.</p>
      <span class = 'help-block'>@if($errors->has('image')) {{$errors->first('image')}} @endif</span>
      @if($data->image)
        <div>
          <img src="{{Config::get('app.url').'public/sms/assets/img/books/medium/'.$data->image}}">
        </div>
      @endif
    </div>
    <div class="form-group @if($errors->has("description")) {{"has-error"}} @endif">
      <label for="content">Description</label>
      <textarea class="textarea" name="description" placeholder="Describe about category here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{Input::old('description')?Input::old('description'):$data->description}}</textarea>
      <span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
    </div>
    <div class="form-group">
      <button class="btn btn-success" type="submit">Save and Close</button>
      <button class="btn btn-primary" type="submit">Save and New</button>
    </div>
    <input type="hidden" name="id" value="{{$data->id}}"/>
    <input type="hidden" name="is_active" value="yes"/>
    {{Form::token()}}
  </form>                  
@stop

@section('custom-js')
  <script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
  
  <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>


  <script type="text/javascript">
    $(function () {

      $("#datemask").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
      $("[data-mask]").inputmask();
      $(".textarea").wysihtml5();

    });
  </script>
@stop