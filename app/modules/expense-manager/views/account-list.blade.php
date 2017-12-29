@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('sms/plugins/iCheck/all.css')}}">

@stop


@section('page-header')    
  <h1>Accounts Manager</h1>
@stop

@section('content')
    <div class="box">
            <div class="box-body">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li @if(Input::old('active_tab') != 'tab_2') class="active" @endif><a href="#tab_1" data-toggle="tab">My Account</a></li>
                  <li @if(Input::old('active_tab') == 'tab_2') class="active" @endif><a href="#tab_2" data-toggle="tab">Create account</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane @if(Input::old('active_tab') != 'tab_2') active @endif" id="tab_1">
                    <table id="pageList" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Account name</th>
                          <th>Total balance</th>
                          <th>Description</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      @define $i = 1;
                      @foreach($account_list as $list)
                        <tr>
                          <td>{{ $i++ }}</td>
                          <td>{{ $list->account_name}}</td>
                          <td>Rs. {{ $list->balance}}</td>
                          <td>{{ $list->description }}</td>
                          <td>
                             <a class="btn btn-success btn-flat " type="button" href="{{route('account-edit', $list->id)}}" data-lity><i class="fa fa-fw fa-edit "></i></a>
                            <a href="{{ URL::route('account-delete', $list->id) }}" onclick = "confirmation(event)" class="btn btn-danger btn-flat"  ><i class="fa fa-fw fa-trash "></i></a>
                          </td>
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </div><!-- tab 1 ends -->
                  <div class="tab-pane @if(Input::old('active_tab') == 'tab_2') active @endif" id="tab_2">
                    <div class="row">
                      <div class="col-sm-6">
                        <form method="POST" action="{{ URL::route('create-account') }}">
                        <input type="hidden" name="active_tab" value="tab_2">
                          <div class="form-group">
                            <label>Account name *</label>
                            <input class="form-control" type="text" name="account_name" value="{{ Input::old('account_name')}}">
                          </div><div style="color:red;">{{ $errors->first('account_name')}}</div>
                          
                          <div class="form-group">
                            <label>Balance *</label>
                            <input class="form-control" type="text" name="balance" value="{{ Input::old('balance')}}">
                          </div><div style="color:red;">{{ $errors->first('balance')}}</div>
                          <div class="form-group">
                            <label for="content">Description</label>
                            <textarea class="textarea" name ="description" placeholder="Insert your note here" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{ Input::old('description')}}</textarea>
                          </div><div style="color:red;">{{ $errors->first('description')}}</div>
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