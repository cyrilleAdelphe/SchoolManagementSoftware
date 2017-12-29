@extends('backend.'.$role.'.main')

@section('custom-css')
	<link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>Progress Report</h1>
@stop
      

@section('content')

<div class="row">
  <div class="col-sm-12" style="margin-bottom:15px">
    <a class="btn btn-default" target="_blank" onclick="printDiv('printableArea')" href="#">
        <i class="fa fa-print"></i>
        Print
    </a>
  </div>
</div>

<div id="printableArea">
  <div class="row">
      <div class="col-sm-3">
        <h4 class="text-red">Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}}</h4>
      </div>
      <div class="col-sm-9 backBtn" style="margin-bottom:15px">
        <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
      </div>
  </div> <!-- row ends -->
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered table-striped last-hide">
        <thead>
          <tr>
            <th>SN</th>
            <th>Name</th>
            <th>Status</th>
            <th>Percentage</th>
            <th>Grade</th>
            <th>Rank</th>
            <th>Remark</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        	@define $i = 1
        	@define $total_passed = 0
        	@define $total_failed = 0
        	@define $total_male_passed = 0
        	@define $total_female_passed = 0
        	@define $total_male_failed = 0
        	@define $total_female_failed = 0
          @foreach($data as $d)
          <tr>

            <td>{{$i++}}</td>
            <td>{{$d->student_name}}</td>
            <td><span class=@if($d->status == 'Passed') "text-green" <?php $total_passed++;  ?> @if($d->sex == "male") <?php $total_male_passed++;  ?> @else <?php $total_female_passed++;  ?> @endif @else "text-red" <?php $total_failed++;  ?> @if($d->sex == "male") <?php $total_male_failed++;  ?> @else <?php $total_female_failed++;  ?> @endif  @endif>{{$d->status}}</span></td>
            <td>{{$d->percentage}}</td>
            <td>{{$d->cgpa}}</td>
            <td>{{$d->rank}}</td>
            <td>{{$d->remarks}}</td>
            <td>
              <a data-toggle="tooltip" title="View detail" href="{{URL::route('report-mass-print')}}?exam_id={{Input::get('exam_id',0)}}&student_id={{$d->student_id}}&class_id={{Input::get('class_id', 0)}}&section_id={{Input::get('section_id', 0)}}" class="btn btn-info btn-flat">
                <i class="fa fa-fw fa-eye"></i>
              </a>
              <a href="#" data-toggle="modal" data-target="#edit-remarks{{$d->id}}" data-toggle="tooltip" title="Edit remark" class="btn btn-success btn-flat" type="button" @if(!AccessController::checkPermission('report', 'can_edit')) disabled @endif>
                <i class="fa fa-fw fa-edit"></i>
              </a>
              @include($module_name.'.views.edit-remarks-modal')
            </td>
          </tr>
          <tr>
          @endforeach
            
        </tbody>
      </table>
    </div>
  </div><!-- row ends -->
  <div class="row">
    <div class="col-sm-12">
      <h4 ><i class="fa fa-fw fa-bar-chart text-green"></i>Report summary</h4>
    </div>
    <div class="col-sm-6">                   
      <table class="table">
        <tr>
          <td><strong>Total students</strong></td>
          <td>{{count($data)}}</td>
        </tr>
        <tr>
          <td><strong>Total passed boys</strong></td>
          <td>{{$total_male_passed}}</td>
        </tr> 
        <tr>
          <td><strong>Total failed boys</strong></td>
          <td>{{$total_male_failed}}</td>
        </tr> 
        <tr>
          <td><strong>Highest percentage</strong></td>
          <td>{{$data[0]->percentage}}% ( {{$data[0]->student_name}} )</td>
        </tr>                     
      </table>
    </div>
    <div class="col-sm-6">
      <table class="table">
        <tr>
          <td><strong>Total passed students</strong></td>
          <td>{{$total_passed}}</td>
        </tr>
        <tr>
          <td><strong>Total passed girls</strong></td>
          <td>{{$total_female_passed}}</td>
        </tr>
        <tr>
          <td><strong>Total failed girls</strong></td>
          <td>{{$total_female_failed}}</td>
        </tr>
      </table>
    </div>
  </div><!-- row ends -->
</div><!-- printable area ends -->

@stop

@section('custom-js')
<script>
      function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;}
    </script>
@stop