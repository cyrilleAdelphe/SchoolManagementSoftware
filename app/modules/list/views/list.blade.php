@extends('backend.superadmin.main')

@section('custom-css')
    <!-- Theme style -->    
    <link href="{{asset('public/plugins/datatables/dataTables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')    
  <h1>Article List Manager</h1>
@stop

  
@section('content')
  <!-- Main content -->


        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Create List Info</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Info List</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <form method="post" action = "{{ URL::route('list-create-post') }}" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" name="title" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('title')) ? (Input::old('title')) : '' }}">
                                <span class = "form-error">
                                    @if($errors->has('title')) 
                                      {{ $errors->first('title') }} 
                                    @endif
                                  </span>
                            </div>
                            <div class="form-group">
                            <label for="profilePic">Upload Icon</label>
                            <input id="profilePic" name='image' type="file">
                            <span class = "form-error">
                              @if($errors->has('image')) 
                                {{ $errors->first('image') }} 
                              @endif
                            </span>
                          </div>
                            <div class="form-group">
                                <label for="content">Short Information</label>
                                <textarea class="textarea" name="information" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ (Input::old('information')) ? (Input::old('information')) : '' }}</textarea>
                                <span class = "form-error">
                                  @if($errors->has('information')) 
                                    {{ $errors->first('information') }} 
                                  @endif
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="title">Order</label>
                                <input id="title" name="order_index" class="form-control" type="text" placeholder="Enter menu title" value= "{{ (Input::old('order_index')) ? (Input::old('order_index')) : '' }}">
                                <span class = "form-error">
                                  @if($errors->has('order_index')) 
                                    {{ $errors->first('order_index') }} 
                                  @endif
                                </span>
                            </div>
                             <div class="form-group">
                              <label for="parent">Status</label>
                              <select class="form-control" name="is_active">
                                <option value="yes" {{ Input::old('is_active')==='yes' ? 'selected="selected"' : '' }}>Enable</option>
                                <option value="no" {{ Input::old('is_active')==='no' ? 'selected="selected"' : '' }}>Disable</option>
                              </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                            {{ Form::token() }}
                        </form>
                  </div>

                  <div class="tab-pane" id="tab_2">
                    <div class="box-header">
                          <h3 class="box-title">Info list</h3>
                        </div><!-- /.box-header -->
                        <table id="" class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>SN</th>
                              <th>Title</th>
                              <th>Content</th>
                              <th>Order No.</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @define $i=1
                            @foreach($list as $item)
                              <tr>
                                <td>{{$i++}}</td>
                                <td>{{$item['title']}}</td>
                                <td>{{$item['information']}}</td>
                                <td>{{$item['order_index']}}</td>
                                <td>
                                      @if($item['is_active']=='no')
                                        <span  data-toggle="tooltip" title="Disable" class="glyphicon glyphicon-minus-sign"></span>
                                      @else
                                        <span style="color:green" data-toggle="tooltip" title="Enable" class="glyphicon glyphicon-ok-sign"></span>
                                      @endif

                                </td>
                                <td>                                      
                                  <a href="{{URL::route('list-edit-get',[$item['id'],$item['title']])}}" data-toggle="tooltip" title="Edit" class="btn btn-info" type="button">
                                      <i class="fa fa-fw fa-edit"></i>
                                    </button>
                                    <a href="{{URL::route('list-delete-get',[$item['id'],$item['title']])}}" data-toggle="tooltip" title="Delete" class="btn btn-danger" type="button">
                                      <i class="fa fa-fw fa-trash"></i>
                                    </button>
                                </td>
                              </tr>
                            @endforeach
                            
                          </tbody>
                        </table>
                  </div>

                </div>
            </div>
        </div>
    </div>
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@stop

@section('custom-js')
  <!-- Editor SCRIPT -->
  <script src="{{asset('public/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script>
  <script type="text/javascript">
    $(function () {
      //bootstrap WYSIHTML5 - text editor
      $(".textarea").wysihtml5();
    });
  </script>
@stop
      
