@extends('backend.superadmin.main')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">

@stop


@section('page-header')    
  <h1>House Manager</h1>
@stop

@section('content')
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li @if(Input::old('active_tab') != 'tab_2') class="active" @endif><a href="#tab_1" data-toggle="tab">House List</a></li>
                  <li @if(Input::old('active_tab') == 'tab_2') class="active" @endif><a href="#tab_2" data-toggle="tab">Create House</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane @if(Input::old('active_tab') != 'tab_2') active @endif" id="tab_1">
                  
                    <table id="pageList" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>House name</th>
                          <th>ID</th>
                          <th>House Code</th>
                          <th>Is Active</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                     @if(count($house))
                      @define $i = 1;
                      @foreach($house as $list)
                      <tr>
                      <td>{{$i++}}</td>
                      <td>{{ $list->house_name}}</td>
                      <td>{{ $list->id}}</td>
                      <td>{{ $list->house_code}}</td>
                      <td>{{ $list->is_active}}</td>
                          <td>
                             <a class="btn btn-success btn-flat btn-sm" type="button" href="{{ URL::route('house-edit', $list->id)}}" data-lity><i class="fa fa-fw fa-edit "></i></a>
                            <a href="{{ URL::route('delete-house', $list->id) }}" onclick = "confirmation(event)" class="btn btn-danger btn-flat btn-sm"  ><i class="fa fa-fw fa-trash "></i></a>
                          </td>
                      @endforeach
                      @else
                     <p style="color:red; text-align:center;">No records</p>
                      @endif
                                   
                      
                      </tbody>
                    </table>
                  </div><!-- tab 1 ends -->
                  <div class="tab-pane @if(Input::old('active_tab') == 'tab_2') active @endif" id="tab_2">
                    <div class="row">
                      <div class="col-sm-6">
                        <form method="POST" action="{{ URL::route('create-house') }}">
                        <input type="hidden" name="active_tab" value="tab_2">
                          <div class="form-group">
                            <label>House name *</label>
                            <input class="form-control" type="text" name="house_name" value="{{ Input::old('house_name')}}">
                          </div><div style="color:red;">{{ $errors->first('house_name')}}</div>
                          
                          <div class="form-group">
                            <label>House Code *</label>
                            <input class="form-control" type="text" name="house_code" value="{{ Input::old('house_code')}}">
                          </div><div style="color:red;">{{ $errors->first('house_code')}}</div>
                          <div class="form-group"  >
                          Is Active:
                            <input type="radio" name="is_active" value="yes">Yes
                            <input type="radio" name="is_active" value="no">no
                            <div style="color:red;">{{ $errors->first('is_active')}}</div>
                          </div>
                           <div class="form-group">
                            <button class="btn btn-success btn-flat btn-lg">Submit</button>
                          </div>
                          {{ Form::token() }}
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
    

@stop

@section('custom-js')
<script type="text/javascript" src ="{{ asset('sms/assets/js/lity.min.js')}}"></script>

<script type="text/javascript">
  function confirmation(event)
  {
    var answer = confirm("Are you sure ?")
    if(!answer) {
      event.preventDefault();
      return false;
    }
    return true;

  }
</script>
    <script type="text/javascript">
      
      setTimeout(function() {
        $('.alert-success').fadeOut('slow');
        }, 2000);
     </script>
    
@stop
@stop