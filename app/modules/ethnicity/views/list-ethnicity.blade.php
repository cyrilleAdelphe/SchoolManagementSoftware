@extends('backend.superadmin.main')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">

@stop


@section('page-header')    
  <h1>Ethnicity Manager</h1>
@stop

@section('content')
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li @if(Input::old('active_tab') != 'tab_2') class="active" @endif><a href="#tab_1" data-toggle="tab">Ethnicity List</a></li>
                  <li @if(Input::old('active_tab') == 'tab_2') class="active" @endif><a href="#tab_2" data-toggle="tab">Create Ethnicity</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane @if(Input::old('active_tab') != 'tab_2') active @endif" id="tab_1">
                  
                    <table id="pageList" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Ethnicity name</th>
                          <th> ID </th>
                          <th>Ethnicity Code</th>
                          <th>Is Active</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      @if(count($ethnicity_list))
                      @define $i = 1;
                      @foreach($ethnicity_list as $list)
                      <tr>
                      <td>{{$i++}}</td>
                      <td>{{ $list->ethnicity_name}}</td>
                       <td>{{ $list->id}}</td>
                      <td>{{ $list->ethnicity_code}}</td>
                      <td>{{ $list->is_active}}</td>
                          <td>
                             <a class="btn btn-success btn-flat btn-sm" type="button" href="{{ URL::route('edit-ethnicity', [$list->id]) }}" data-lity><i class="fa fa-fw fa-edit "></i></a>
                            <a href="{{ URL::route('delete-ethnicity', $list->id) }}" onclick = "confirmation(event)" class="btn btn-danger btn-flat btn-sm"  ><i class="fa fa-fw fa-trash "></i></a>
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
                        <form method="POST" action="{{ URL::route('ethnicity-create') }}">
                        <input type="hidden" name="active_tab" value="tab_2">
                          <div class="form-group">
                            <label>Ethnicity name *</label>
                            <input class="form-control" type="text" name="ethnicity_name" value="{{ Input::old('ethnicity_name')}}">
                          </div><div style="color:red;">{{ $errors->first('ethnicity_name')}}</div>
                          
                          <div class="form-group">
                            <label>Ethnicity Code *</label>
                            <input class="form-control" type="text" name="ethnicity_code" value="{{ Input::old('ethnicity_code')}}">
                          </div>
                          <div style="color:red;">{{ $errors->first('ethnicity_code')}}</div>
                          <div class="form-group" >
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