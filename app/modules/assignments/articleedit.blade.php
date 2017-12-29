@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
  <!-- Theme style -->    
    <link href= "{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" type="text/css" />
     <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Edit Article</h1>
@stop

@section('content')

  <!-- Main content -->
<div class="content">
<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
           <form method = "post" action = "{{URL::route('articles-edit-post')}}">
            <?php
              /*
          | This segment of code select value to be placed in the form field
          | When the edit button is pressed from the articles' main page, you would want the values in database.
          | When the user has edited the article but the input isn't validated, you would want the edited values
              */
              $value = [];
              $fields = ['title','alias','content', 'is_featured','meta_tag','meta_description'];
              foreach($fields as $field)
              {
                $value[$field] = '';
                if (Input::old($field))
                {
                  $value[$field] = Input::old($field);
                }
          else
          {
                   $value[$field] = $article[$field];
          }
        }
            ?>
                <div class="form-group">
                  <label for="title">Title</label>
                    <input name = "title" id="title" class="form-control" type="text" placeholder="Enter article title" value = "{{ $value['title'] }}" >
                    <span class = "form-error">@if($errors->has('title')) {{ $errors->first('title') }} @endif</span>
                </div>
                <div class="form-group">
                    <label for="title">Alise</label>
                    <input name = "alias" id="alise" class="form-control" type="text" placeholder="Enter unique alise" value= "{{ $value['alias'] }}">
                    <span class = "form-error">@if($errors->has('alias')) {{ $errors->first('alias') }} @endif</span>
                </div>

                <div class="form-group">
                  <label for="title">Meta Tag</label>
                  <input name = "meta_tag" id="meta_tag" class="form-control" type="text" placeholder="Enter meta tag" value= "{{ $value['meta_tag'] }}">
                  <span class = "form-error">@if($errors->has('meta_tag')) {{ $errors->first('meta_tag') }} @endif</span>
                </div>

                <div class="form-group">
                  <label for="content">Description</label>
                  <textarea name = "meta_description" placeholder="Place some text here" class="form-control" row="3">{{ $value['meta_description'] }}</textarea>
                  <span class = "form-error">@if($errors->has('meta_description')) {{ $errors->first('meta_description') }} @endif</span>
                </div>

                <div class="form-group">
                  <label for="category"> Category </label>
                  <select name="category_id" class="form-control">
                    @foreach($categories as $category)
                      <option {{ $category['id'] == $article['category_id'] ? 'selected="selected"' : ''}} value= "{{ $category['id'] }}"> {{ $category['title'] }} </option>
                    @endforeach
                </select>
              </div>

                <div class="form-group">
                    <label for="content">Article content</label>
                    <textarea id='editor1' name = "content" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ $value['content'] }}</textarea>
                    <span class = "form-error">@if($errors->has('content')) {{ $errors->first('content') }} @endif</span>
                </div>

                <div class = "form-group">
                    <label for = "is_featured">Is Featured:</label>&nbsp;&nbsp;
                    <input type = "radio" name = "is_featured" value = "yes" @if( $value['is_featured']  == 'yes') checked @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type = "radio" name = "is_featured" value = "no" @if($value['is_featured'] == 'no') checked @endif>No
                </div>

                <div class="form-group">
                    <button class="btn btn-success btn-flat btn-lg" type="submit">Update</button>
                </div>
                <input type="hidden" name="id" value="{{$article['id']}}"/>
                {{Form::token()}}
            </form>
</div>
  
@stop

@section('custom-js')

  <!-- Editor SCRIPT -->
  <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script type="text/javascript">
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();
    });
  </script>
  
  <script src="{{asset('sms/plugins/ckeditor/ckeditor.js')}}"></script>
  
 <script>
      $(document).ready(function()
      {
        var roxyFileman = "{{Config::get('app.url')}}/vendor/text-editor/fileman?integration=ckeditor";
        $(function()
        {
          CKEDITOR.replace( "editor1",{filebrowserBrowseUrl:roxyFileman,
                    filebrowserImageBrowseUrl:roxyFileman+"&type=image",
                    removeDialogTabs: "link:upload;image:upload"});
        });
        
      });
  </script>;


   <script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>
  
@stop