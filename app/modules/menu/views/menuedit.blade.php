@extends('backend.'.$role.'.main')

@section('custom-css')
    <!-- Theme style -->    
    <link href="{{asset('sms/plugins/datatables/dataTables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Menu Manager</h1>
@stop

@section('content')

  <?php
    /**
      * This segment of code select value to be placed in the form field
      * When the edit button is pressed from the menu main page, you would want the values in database.
      * When the user has edited the menu but the input isn't validated, you would want the edited values
     */
    
    $input_value = [];
    $fields = ['title','alias','article_id','parent_id','is_active'];
    foreach($fields as $field)
    {
      $input_value[$field] = Input::old($field) ? Input::old($field) : $menu[$field];

    }

  ?>

<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
            <form method="post" action = "{{ URL::route('menu-edit-post') }}" enctype="multipart/form-data">
              <div class="form-group">
                <label for="title">Title</label>
                <input id="title" name="title" class="form-control" type="text" 
                            value= "{{ (Input::old('title')) ? (Input::old('title')) : $menu['title'] }}">
                <span class = "form-error">
                  @if($errors->has('title')) 
                    {{ $errors->first('title') }} 
                  @endif
                </span>
              </div>
                <div class="form-group">
                    <label for="alise">Alise</label>
                    <input id="alise" name="alias" class="form-control" type="text" placeholder="Enter unique alise"
                        value= "{{ (Input::old('alias')) ? (Input::old('alias')) : $menu['alias'] }}">
                    <span class = "form-error">
                      @if($errors->has('alias')) 
                        {{ $errors->first('alias') }} 
                      @endif
                    </span>
                </div>

                <div class="form-group">
                    <label for="external_link">External Link</label>
                    <input id="external_link" name="external_link" class="form-control" type="text" placeholder="Enter external link"
                        value= "{{ (Input::old('external_link')) ? (Input::old('external_link')) : $menu['external_link'] }}">
                    <span class = "form-error">
                      @if($errors->has('external_link')) 
                        {{ $errors->first('external_link') }} 
                      @endif
                    </span>
                </div>
          <div class="form-group">
              <label for="article"><a href="#" data-toggle="modal" data-target="#article">Select Articles</a></label>
              <div id="article" class="modal fade" role="dialog">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Articles list</h4>
                      </div>
                      <div class="modal-body">
                        <table id="pageList" class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>SN</th>
                              <th>Article Title</th>
                              <th>Creation detail</th>
                              <th>Selected</th>
                            </tr>
                          </thead>
                          <tbody>
                            
                            @define $i = 1
                            <tr>
                              <td>{{$i++}}</td>
                              <td>N/A (Parent Menu/External Link)</td>
                              <td></td>
                              <td><input type="radio" name="article_id" value=0 {{ $input_value['article_id']==null ? 'checked="checked"' : '' }}></td>
                            </tr>
                            @foreach($articles as $article)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$article['title']}}</td>
                                <td>{{$article['created_by']}}</td>
                                <td><input type="radio" name="article_id" value="{{$article['id']}}" {{ $article['id']==$input_value['article_id'] ? 'checked="checked"' : '' }}></td>
                              </tr>
                            @endforeach
                            
                            
                          </tbody>
                        </table>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
              </div>
              @if($errors->has('article_id')) 
                {{ $errors->first('article_id') }} 
              @endif
          </div>
          <div class="form-group">
            <label for="parent">Parent Item</label>
            {{ HelperController::generateSelectListWithDefault('Menus', 'title', 'id', 'parent_id', $input_value['parent_id'], 
                                $condition = array(['field_name'=>'article_id','value'=>null])
                                ,'form-control','Root') }}
          </div>

          <div class = 'form-group @if($errors->has("profile_pic")) {{"has-error"}} @endif'>
              <label for = 'profile_pic'  class = 'control-label'>Upload New Picture</label>
              <input type = 'file' name = 'profile_pic'><span class = 'help-block'>@if($errors->has('profile_pic')) {{$errors->first('profile_pic')}} @endif</span>
              <img class="img-responsive" src = "{{ Config::get('app.url').'/app/modules/menu/asset/images/'. $menu['id'] .'.jpg'}}">
          </div>

          <div class="form-group">
            <label for="parent">Status</label>
              <select class="form-control" name="is_active">
                <option value="yes" {{ $input_value['is_active']=='yes' ? 'selected="selected"' : '' }}>Enable</option>
                <option value="no" {{ $input_value['is_active']=='no' ? 'selected="selected"' : '' }}>Disable</option>
              </select>
          </div>

          <div class="form-group">
              <label for="title">Order</label>
              <input id="title" name="order_index" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('order_index')) ? (Input::old('order_index')) : $menu['order_index'] }}">
              <span class = "form-error">
                @if($errors->has('order_index')) 
                  {{ $errors->first('order_index') }} 
                @endif
              </span>
          </div>

          <div class="form-group">
              <button class="btn btn-primary btn-lg btn-flat" type="submit">Submit</button>
          </div>
          <input type="hidden" name="id" value="{{ $menu['id'] }}">
          {{ Form::token() }}
        </form>
      
@stop

@section('custom-js')
  <!-- DATA TABES SCRIPT -->
    <script src="{{asset('sms/plugins/datatables/jquery.dataTables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/datatables/dataTables.bootstrap.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
      $(function () {
        $("#pageList").dataTable();
        
      });
    </script>
@stop