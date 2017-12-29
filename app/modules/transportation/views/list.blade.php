@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>Transportation</h1>
@stop

@section('content')

<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class = "active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-fw fa-bus"></i> Vehicle list</a></li>
    <li ><a href="#tab_2" data-toggle="tab"><i class="fa fa-fw fa-plus-square"></i> Assign student vehicle</a></li>
    <li ><a href="#tab_4" data-toggle="tab"><i class="fa fa-fw fa-plus-square"></i> Assign staff vehicle</a></li>
    <li><a href="#tab_3" data-toggle="tab"><i class="fa fa-fw fa-bus"></i>Student List</a></li>
    <li><a href="#tab_5" data-toggle="tab"><i class="fa fa-fw fa-bus"></i>Staff List</a></li>
  </ul>
  <div class="tab-content">
    <div id="tab_1" class="tab-pane active" >
      <div class="col-sm-3 col-sm-offset-9" style="margin-bottom:15px">
          <a href="{{URL::route('transportation-create-get')}}" class="btn btn-success pull-right">Register new vehicle</a>
        </div>
      <table id="pageList" class="table table-bordered table-striped">
        @if($data['count'])
        <thead>
          <tr>
            <th>SN</th>
            <th>Plate number</th>
            <th>Bus ID</th>
            <th>Route</th>
             <th>Route Number</th>
            <th>Driver's number</th>
            <th>Assigned students</th>
            <th>Assigned Staffs</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        
        	@define $i = 1
          @foreach($data['data'] as $d)
          <tr>
            <td>{{$i++}}</td>
            <td>{{$d->number_plate}}</td>
            <td>{{$d->bus_code}}</td>
            <td>{{$d->route}}</td>
            <td>{{$d->route_number}}</td>
            <td>{{$d->driver_number}}</td>
            <td>@if(isset($data['count_data'][$d->id])) {{$data['count_data'][$d->id]}} @else 0 @endif</td>
            <td>@if(isset($data['d'][$d->id])) {{count($data['d'][$d->id])}} @else 0 @endif
            <td>
              <a data-toggle="tooltip" title="Locate" href="{{URL::route('transportation-view-locations', array($d->unique_transportation_id))}}" class="btn btn-info btn-flat"> <i class="fa fa-fw fa-map-marker"></i> </a>
              <a data-toggle="tooltip" title="Edit" href="{{URL::route('transportation-edit-get', array($d->id))}}" class="btn btn-success btn-flat"> <i class="fa fa-fw fa-edit"></i> </a>
               
               <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('transportation', 'can_delete')) disabled @endif>
                <i class="fa fa-fw fa-trash"></i> Delete
               </a>
               @include('transportation.views.transportation-delete-modal')
            </td>
          </tr>
          @endforeach
       
        </tbody>
        @else
        <tr><td><div class="alert alert-warning alert-dismissable">
        <h4><i class="icon fa fa-warning"></i>No Data Found</h4>
        </div></td></tr>
        @endif
      </table>
    </div><!-- tab 1 ends -->
    <div class="tab-pane" id="tab_2" >
      <form method = "post" action = "{{URL::route('transportation-assign-students')}}">
      <input type="hidden" name="active_tab" value="tab_2">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group @if($errors->has('transportation_id')) {{'has-error'}}} @endif ">
            <label>Choose vehicle</label>
            {{HelperController::generateSelectList('Transportation', 'bus_code', 'id', 'transportation_id', Input::old('transportation_id'))}}
            <span class = 'help-block'>@if($errors->has('transporation_id')) {{$errors->first('transporation_id')}} @endif</span>
          </div>
          
          <div class="form-group @if($errors->has('student_id')) {{'has-error'}} @endif ">
            <label>Student's Username</label>
          <input id="studentUsername" class="form-control" type="text" name = "student_id" value="{{Input::old('student_id')}}" required> 
            <span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
            <a class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
            
            <input type="hidden" id="module_name" value="{{ $module_name }}">
            @include('include.modal-find-student')
          </div>
          
          <div class="form-group @if($errors->has('fee_amount')) {{'has-error'}} @endif ">
            <label>Fee</label>
            <input id="fee_amount" class="form-control" type="text" name = "fee_amount" value = "{{Input::old('fee_amount')}}" required> 
            <span class = 'help-block'>@if($errors->has('fee_amount')) {{$errors->first('fee_amount')}} @endif</span>
          </div>

          <div class="form-group">
            <input type = "hidden" name = "is_active" value = "yes">
            <button type="submit" class="btn btn-success btn-lg btn-flat" >Submit</button>
          </div>
        </div>
      </div>
      {{Form::token()}}
      </form>
    </div><!-- tab 2 ends -->

    
    
    <div class="tab-pane" id="tab_4">
      <form method = "post" action = "{{ URL::route('transportation-assign-staffs') }}">
      <div class="row">
      <input type="hidden" name="tab" value="tab_4">
        <div class="col-sm-12">
          <div class="form-group @if($errors->has('transportation_id')) {{'has-error'}}} @endif ">
            <label>Choose vehicle</label>
            {{HelperController::generateSelectList('Transportation', 'bus_code', 'id', 'transportation_id', Input::old('transportation_id'))}}
            <span class = 'help-block'>@if($errors->has('transporation_id')) {{$errors->first('transporation_id')}} @endif</span>
          </div>
          
          <div class="form-group @if($errors->has('employee_id')) {{'has-error'}} @endif ">
            <label>Employee's Username</label>
            <input id="employeeUsername" class="form-control" type="text" name = "employee_id" value = "{{Input::old('employee_id')}}" required> 
            <span class = 'help-block'>@if($errors->has('employee_id')) {{$errors->first('employee_id')}} @endif</span>
            <a class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#find-id2" ><i class="fa fa-search"></i> Find ID</a>
            @include('include.modal-find-staff')
          </div>

          <div class="form-group">
          	<input type = "hidden" name = "is_active" value = "yes">
            <button type="submit" class="btn btn-success btn-lg btn-flat" >Submit</button>
          </div>
        </div>
      </div>
      {{Form::token()}}
      </form>
    </div><!-- tab 5 ends -->
     <div class="tab-pane" id="tab_5">
      
      <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <label>Select Bus</label>
          {{HelperController::generateSelectListWithDefault
                ('Transportation', 'bus_code', 'id', 'bus_id', 0, array(), 'form-control','All')}}
        </div>

      </div>
      <div id = "staff-list"></div>
      
      </div>
      {{Form::token()}}
      </form>
    </div><!-- tab 5 ends -->


        

    <div class="tab-pane" id="tab_3">
      <div class="col-sm-3 col-sm-offset-9" style="margin-bottom:15px">
        <a href="{{URL::route('transportation-create-get')}}" class="btn btn-success pull-right">Register new vehicle</a>
      </div>

      <div class="col-sm-4">
        <div class="form-group">
          <label>Select Bus</label>
          <br>
          <select name = "busId" id = "busId" class="form-control">
            <option value="0">All</option>
            @foreach($data['transportation'] as $index => $key)
              <option value="{{$index}}">{{ $key}}</option>
            @endforeach
          </select>
        </div>
      </div>
      
      <div class="col-sm-4">
        <div class="form-group">
          <label>Search by Student Name </label>
          <br>
           <input type="text" name="search" id="search" placeholder="Student Name" class="form-control">

        </div>
      </div>

      <div id="student-list">
      </div>
      
    </div>
  </div>  
</div>
@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

<script type="text/javascript">
  // event trigger for select button after searching student
  function findIdSelect(username) {
    /*$('#studentUsername').val(function(){

        
        return this.value + username + ',';

   });

    $('#employeeUsername').val(function(){


      return this.value + username + ',';

    });*/

    $('#studentUsername').val(username);
    $('#employeeUsername').val(username);
  }

</script>

<script>
function updateStudentList()
{
  var id = $('#busId').val();

  $('#student-list').html('<div class = "dloading"><img src = "{{ asset('sms/assets/img/loading.gif')}}"><br/>loading...</div>');
     $.get("{{URL::route('ajax-transportation-student-list')}}",
        {
          'bus_id': id,
          '_token': "{{csrf_token()}}"
        },
        function(data) {
          $('#student-list').html(data);
        });
}

function updateStaffList()
{
    $('#staff-list').html('<div class = "dloading"><img src = "{{ asset('sms/assets/img/loading.gif')}}"><br/>loading...</div>');
  $.get("{{URL::route('transportation-staff-list')}}",
        {
          'bus_id': $('#bus_id').val(),
          '_token': "{{csrf_token()}}"
        },
        function(data) {
          $('#staff-list').html(data);
        });
}

$(function() {
  updateStudentList();
  updateStaffList();

  $(document).on('click', '#select_checkbox', function() {

    updateSelectedCheckBox();

  });

    $(document).on('click', '#staff_checkbox', function() {

    updateSelectedStaffCheckBox();

  });
  
  $(document).on('change', '#bus_id', function() {
    
    updateStaffList();
  });
  $(document).on('change', '#busId', function(){

    updateStudentList();
  })
});

  function updateSelectedCheckBox()
  {
    var list = [];
    $.each($("input[name='select_student']:checked"), function(){
        list.push($(this).val().trim());

    });
     $('#studentUsername').val(list) + ',';
     

    
  }

  function updateSelectedStaffCheckBox()
  {
    
    var list = [];
    $.each($("input[name='select_staff']:checked"), function(){
        list.push($(this).val().trim());

    });
     $('#employeeUsername').val(list) + ',';
  }

</script>

 <script type="text/javascript">
      var keyupfunction = function() {
          var search_assigned_student = $("#search").val();

               $('#student-list').html('<div class = "dloading"><img src = "{{ asset('sms/assets/img/loading.gif')}}"><br/>loading...</div>');
            $.ajax({
                url: '{{ URL::route('search-transportation-student')}}',
                data: {'search_assigned_student':search_assigned_student},
                type: 'get',

                success:function(data)
                {
                  $('#student-list').html(data);
                }

            });
      }

      $(document).ready(function() {
        $('#search').keyup(keyupfunction);

      });
   </script>

@stop
