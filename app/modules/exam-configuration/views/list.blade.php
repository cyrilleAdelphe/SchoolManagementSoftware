@extends('exam-configuration.views.tabs')

@section('tab-content')

<table id="pageList" class="table table-bordered table-striped">
  @if(count($data['data']))
  <thead>
    <tr>
      <th>SN</th>
      <th>Exam Name</th>
      <th>Session</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Weightage %</th>
      <th>Remarks</th>
      <th>Action</th>
    </tr>
  </thead>
  
  <tbody id="subject_list">
      
        @define $i = 1 
        @foreach($data['data'] as $d)
          <tr>
            <td>{{$i++}}</td>
            <td>{{$d->exam_name}}</td>
            <td>{{$d->session_name}}</td>
            <td>{{DateTime::createFromFormat('Y-m-d',$d->exam_start_date_in_ad)->format('d F Y')}}</td>
            <td>{{DateTime::createFromFormat('Y-m-d',$d->exam_end_date_in_ad)->format('d F Y')}}</td>
            <td>{{$d->weightage}}</td>
            <td>{{$d->remarks}}</td>
            <td>
            <a href="{{URL::route('exam-configuration-admit-card', $d->id)}}"
                data-toggle="tooltip" title="Print Admit Card" class="btn btn-success btn-flat" type="button" @if(AccessController::checkPermission('exam-configuration', 'can_generate_admit_card') == false) disabled @endif>
                <i class="fa fa-fw fa-eye"></i>
              </a>
              <a href="#" data-toggle="modal" data-target="#edit{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-success btn-flat" type="button" @if(AccessController::checkPermission('exam-configuration', 'can_edit') == false) disabled @endif>
                <i class="fa fa-fw fa-edit"></i>
               </a>
               @include('exam-configuration.views.edit-modal')

              <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('exam-configuration', 'can_delete') == false) disabled @endif>
                <i class="fa fa-fw fa-trash"></i>
              </a>
               @include('exam-configuration.views.delete-modal')
            </td>
          </tr>
        @endforeach
      
    
  </tbody>
  @else
          <div class="alert alert-warning alert-dismissable">
      <h4><i class="icon fa fa-warning"></i>No Data Found</h4>
    
  </div>

        @endif
</table>

@stop

@section('custom-js')
<script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>

    <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
      $(function () {
        //Datemask dd/mm/yyyy
        $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        $("[data-mask]").inputmask();  

        //Timepicker
        $(".timepicker").timepicker({
          showInputs: false
        });      
      });
    </script>

@stop
