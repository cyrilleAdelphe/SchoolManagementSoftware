@extends('backend.'.$role.'.main')

@section('custom-css')
    <!-- Theme style -->    
    <link href="{{asset('sms/plugins/datatables/dataTables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Menu Manager</h1>
@stop

@section('content')
	<!-- Main content -->
              <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                      <li class="active"><a href="#tab_1" data-toggle="tab">Create Menus</a></li>
                      <li><a href="#tab_2" data-toggle="tab">Menu List</a></li>
                  </ul>
                  <div class="tab-content">
                      <div class="tab-pane active" id="tab_1">
                          <form method="post" action = "{{ URL::route('menu-create-post') }}" enctype = "multipart/form-data">
                              <div class="form-group  @if($errors->has("title")) {{"has-error"}} @endif">
                                  <label for="title">Title</label>
                                  <input id="title" name="title" class="form-control" type="text" placeholder="Enter menu title"
                                  				value= "{{ (Input::old('title')) ? (Input::old('title')) : '' }}">
                                  <span class = "help-block">
																		@if($errors->has('title')) 
																			{{ $errors->first('title') }} 
																		@endif
																	</span>
                              </div>
                              <div class="form-group  @if($errors->has("alias")) {{"has-error"}} @endif">
                                  <label for="alise">Alise</label>
                                  <input id="alise" name="alias" class="form-control" type="text" placeholder="Enter unique alise"
                                  		value= "{{ (Input::old('alias')) ? (Input::old('alias')) : '' }}">
                                  <span class = "help-block">
																		@if($errors->has('alias')) 
																			{{ $errors->first('alias') }} 
																		@endif
																	</span>
                              </div>
                              <div class="form-group">
                                  <label for="external_link">External Link</label>
                                  <input id="external_link" name="external_link" class="form-control" type="text" placeholder="Enter external link for the menu"
                                      value= "{{ (Input::old('external_link')) ? (Input::old('external_link')) : '' }}">
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
                                                  <td><input type="radio" name="article_id" value=0 checked="checked" }}></td>
                                                </tr>
                                              	@foreach($articles as $article)
                                              		<tr>
	                                              		<td>{{$i++}}</td>
	                                              		<td>{{$article['title']}}</td>
	                                              		<td>{{$article['created_by']}}</td>
	                                              		<td><input type="radio" name="article_id" value="{{$article['id']}}" @if(Input::old('article_id')==$article['id']) checked @endif></td>
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

                              <div class = 'form-group @if($errors->has("profile_pic")) {{"has-error"}} @endif'>
                                  <label for = 'profile_pic'  class = 'control-label'>Upload Picture</label>
                                  <input type = 'file' name = 'profile_pic'><span class = 'help-block'>@if($errors->has('profile_pic')) {{$errors->first('profile_pic')}} @endif</span>
                              </div>
                              
                              <div class="form-group">
                                <label for="parent">Parent Item</label>
                                {{ HelperController::generateSelectListWithDefault('Menus', 'title', 'id', 'parent_id', $selected = '', 
                                										$condition = array(['field_name'=>'article_id','value'=>NULL])
                                										,'form-control','Root') }}
                              </div>
                              <div class="form-group  @if($errors->has("order_index")) {{"has-error"}} @endif">
                                  <label for="title">Order</label>
                                  <input id="title" name="order_index" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('order_index')) ? (Input::old('order_index')) : '' }}">
                                  <span class = "help-block">
                                    @if($errors->has('order_index')) 
                                      {{ $errors->first('order_index') }} 
                                    @endif
                                  </span>
                              </div>
                               <div class="form-group">
                                <label for="parent">Status</label>
                                <select class="form-control" name="is_active">
                                  <option value="yes">Enable</option>
                                  <option value="no">Disable</option>
                                </select>
                              </div>

                              <div class="form-group">
                                  <button class="btn btn-primary btn-lg btn-flat" type="submit">Submit</button>
                              </div>
                              {{ Form::token() }}
                          </form>
                      </div>
                      <div class="tab-pane" id="tab_2">
                          <div class="box-header">
                            <h3 class="box-title">Articles list</h3>
                          </div><!-- /.box-header -->
                          <table id="pageList" class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>SN</th>
                                <th>Menu Title</th>
                                <th>Parent Item</th>
                                <th>Article</th>
                                <th>External Link</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              @define $i = 1
                              @foreach($menus as $menu)
	                              <tr>
	                                <td>{{$i++}}</td>
	                                <td>{{$menu['title']}}</td>
	                                <td>{{ $menu['parent_id'] ? Menus::select('title')->where('id',$menu['parent_id'])->first('title')['title'] : 'Root' }}</td>
	                                <td>{{ $menu['article_id'] ? Articles::select('title')->where('id',$menu['article_id'])->first('title')['title'] : '' }}</td>
	                                <td>{{ $menu['external_link'] }}
                                  <td>{{ $menu['order_index'] }}</td>
	                                <td>
	                                	@if($menu['is_active'] == 'yes')
	                                    <span style="color:green" data-toggle="tooltip" title="Enable" class="glyphicon glyphicon-ok-sign"></span>
	                                  @else
	                                  	<span  data-toggle="tooltip" title="Disable" class="glyphicon glyphicon-minus-sign"></span>
	                                  @endif
	                                </td>

	                                <td>                                      
	                                  <a href="{{ URL::route('menu-edit-get',[$menu['id'],$menu['alias']]) }}" data-toggle="tooltip" title="Edit" class="btn btn-success btn-flat" type="button">
	                                      <i class="fa fa-fw fa-edit "></i>
	                                    </a>
	                                    <a href="{{ URL::route('menu-delete-get',[$menu['id'],$menu['alias']]) }}" data-toggle="tooltip" title="Delete" class="btn btn-danger  btn-flat" type="button">
	                                      <i class="fa fa-fw fa-trash"></i>
	                                    </a>
	                                </td>
	                            	@endforeach
                              </tr>
                            </tbody>
                          </table>
                      </div>                          
                  </div>
              </div>
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

