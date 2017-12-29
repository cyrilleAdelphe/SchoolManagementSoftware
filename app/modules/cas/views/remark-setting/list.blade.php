@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>
   Update Your Remarks
  </h1>
@stop

@section('content')

              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li><a href="{{URL::route('cas-grade-settings-list')}}">Grading</a></li>
                  <li><a href="{{URL::route('cas-sub-topics-list')}}">Additional topics</a></li>
                  <li class="active"><a href="{{URL::route('remark-setting-list')}}">Remarks setting</a></li>
                </ul> 
                <div class="tab-content">
                  

                  <div class="tab-pane" id="tab_3">
                  </div><!-- tab 3 ends -->
                  <div class="tab-pane active" id="tab_4">
                    <div class="item-title">Remarks settings</div>
                    <table  class="table table-bordered table-striped ">
                      @if(count($data))
                      <thead>
                        <tr>
                          <th>Number</th>
                          <th>Remark</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                       
                      @foreach($data as $d)
                        <tr>
                          <td>{{$d->remarks_number}}</td>
                          <td style="max-width: 650px">{{$d->remarks}}</td>
                          <td>
                            <a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(AccessController::checkPermission('academic-session', 'can_edit') == false) disabled @endif><i class="fa fa-fw fa-edit"></i></button></a>
                            
                          <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('academic-session', 'can_delete') == false) disabled @endif>
                    <i class="fa fa-fw fa-trash"></i>
                  </a>
                  @include('include.modal-delete')
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
<form class="" action="{{URL::route('remark-setting-add')}}" method="post">
                    <div class="row">
                      <div class="col-sm-1">
                        <div class="form-group">
                          <label>Number</label>
                          <input name="remarks_number[]" class="form-control" />
                        </div>
                      </div>
                      <div class="col-sm-9">
                        <div class="form-group">
                          <label>Remark</label>
                          <input name="remarks[]" class="form-control" />
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label style="color: #fff; display: block">Add</label>
                          <a href="#" class="btn btn-primary btn-flat add_field_button">Add more</a>
                        </div>
                      </div>
                    </div><!-- row ends -->


                    <div class="input_fields_wrap">
                    </div>

                    <div class="row">
                      <div class="col-sm-12">
                        <button type="submit" class="btn btn-lg btn-flat btn-success">Submit</button>
                      </div>
                    </div>
{{Form::token()}}
</form>

                  </div><!-- tab 4 ends -->
                </div>
              </div>
@stop

@section('custom-js')
    <script type="text/javascript">
      $(document).ready(function() {
          var max_fields      = 10; //maximum input boxes allowed
          var wrapper         = $(".input_fields_wrap"); //Fields wrapper
          var add_button      = $(".add_field_button"); //Add button ID
          
          var x = 1; //initlal text box count
          $(add_button).click(function(e){ //on add input button click
              e.preventDefault();
              if(x < max_fields){ //max input box allowed
                  x++; //text box increment
                  $(wrapper).append('<div class="addedField"><div class="row"><div class="col-sm-1"><div class="form-group"><label>Number</label><input name="remarks_number[]" class="form-control" /></div></div><div class="col-sm-9"><div class="form-group"><label>Remark</label><input name="remarks[]" class="form-control" /></div></div><div class="col-sm-2"><div class="form-group"><label class="form-label" style="color: #fff; display: block">Remove</label><a href="#" class="remove_field btn btn-danger btn-flat">Remove</a></div></div></div></div>'); //add input box
              }
          });
          
          $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            
              e.preventDefault(); $(this).parent().parent().parent().parent().remove(); x--;
          })
      });
    </script>
@stop