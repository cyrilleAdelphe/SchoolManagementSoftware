@extends('backend.superadmin.main')

@section('custom-css')
    <!-- Theme style -->    
    <link href="{{asset('sms/plugins/datatables/dataTables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
@stop    

@section('page-header')    
  <h1>Article Block Manager</h1>
@stop

@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="box">
        <div class="box-body">
        	@define $input_value = [];
			    @define $fields = ['title','class','is_active'];
			    @foreach($fields as $field)
			      @define $input_value[$field] = Input::old($field) ? Input::old($field) : $item[$field];
			    @endforeach

			   
        	<form method="post" action = "{{ URL::route('blocks-edit-post') }}" enctype="multipart/form-data">
              <div class="form-group">
                  <label for="title">Title</label>
                  <input id="title" name="title" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('title')) ? (Input::old('title')) : $item['title'] }}">
                  <span class = "form-error">
                      @if($errors->has('title')) 
                        {{ $errors->first('title') }} 
                      @endif
                    </span>
              </div>

              <div class="form-group">
	              <label for="profilePic">Upload New Picture</label>
	              <input id="profilePic" name='image' type="file">
	              <span class = "form-error">
	                @if($errors->has('image')) 
	                  {{ $errors->first('image') }} 
	                @endif
	              </span>
            	</div>

              <div class="form-group">
                  <label for="content">Article content</label>
                  <textarea class="textarea" name="information" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('information')) ? (Input::old('information')) : $item['information'] }}</textarea>
                  <span class = "form-error">
                    @if($errors->has('information')) 
                      {{ $errors->first('information') }} 
                    @endif
                  </span>
              </div>
              <div class="form-group">
                  <label for="title">Order</label>
                  <input id="title" name="order_index" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('order_index')) ? (Input::old('order_index')) : $item['order_index'] }}">
                  <span class = "form-error">
                    @if($errors->has('order_index')) 
                      {{ $errors->first('order_index') }} 
                    @endif
                  </span>
              </div>
              <div class="form-group">
                <label for="parent">Class</label>
                <select class="form-control" name="class">
                  <option value="blue-bg" {{ $input_value['class'] ==='blue-bg' ? 'selected="selected"' : '' }}>blue-bg</option>
                  <option value="grey-bg" {{ $input_value['class'] ==='grey-bg' ? 'selected="selected"' : '' }}>grey-bg</option>
                </select>
              </div>
              <div class="form-group">
                <label for="parent">Status</label>
                <select class="form-control" name="is_active">
                  <option value="yes" {{ $input_value['is_active'] ==='yes' ? 'selected="selected"' : '' }}>Enable</option>
                  <option value="no" {{ $input_value['is_active'] ==='no' ? 'selected="selected"' : '' }}>Disable</option>
                </select>
              </div>
              <div class="form-group">
                  <button class="btn btn-primary" type="submit">Submit</button>
              </div>
              <input type="hidden" name="id" value="{{ $item['id'] }}">
              {{ Form::token() }}
          </form>
        </div>
    </div>
  </section>
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
@stop


