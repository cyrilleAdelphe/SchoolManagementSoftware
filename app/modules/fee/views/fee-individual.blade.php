@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>
    Payments Detail
  </h1>
@stop

@section('content')
<div class="row">
	<div class="col-sm-3">
    <div class="form-group">
      <label>Select Session</label>
      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
        $selected = 
          Input::has('academic_session_id') ?
          Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label>Enter Student ID</label>
      <input type="text" id="student_id" class="form-control" @if(Input::has('student_id')) value="{{Input::get('student_id')}}" @endif />
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label>Month</label>
      @if(CALENDAR=='BS')
        @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
        @define $months = array('ALL', 'Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra');
      @else
        @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
        @define $months = array('ALL', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')
      @endif
      
      <select class="form-control" name="month" id="month">
      @foreach($months as $key=>$month_name)
      	@define $month_id = $key
      	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month_name}}</option>
      @endforeach
      </select>
    </div>
  </div>
</div><!-- row ends -->

<div id="studentInfo">
</div>

<input type="hidden" id="query_url" value="{{URL::route('fee-fee-individual-info-get')}}" />
<input type="hidden" id="_token" value="{{csrf_token()}}" />
{{-- <button id="getForm" class="btn btn-success" type="submit">Search</button> --}}

<br /><br />

<div id="feeInfo">
</div>
@stop

@section('custom-js')
	<script>
		function updateFeeInfo() {
			if(!$('#student_id').val() || !$('#month').val())
			{
				return;
			}

      $('#feeInfo').html('<div class="dloading">Loading...<br/><img src="{{asset('sms/assets/img/loading.gif')}}" /></div><br/><br/>');
			$.get($('#query_url').val(),
							{
								'academic_session_id':$('#academic_session_id').val(),
								'student_id':$('#student_id').val(),
								'month':$('#month').val(),
								'_token':$('#_token').val()
							},
							function(data, status) {
								if(status) {
									$('#feeInfo').html(data);	
								}
							}
						);
		}
    $(document).on('change', '#academic_session_id', function() {
      updateFeeInfo();
    });

    $(document).on('change', '#month', function() {
      updateFeeInfo();
    });

    $('#student_id').keyup(function(e) {
      if(e.which == 13) {
        updateFeeInfo();
      }
    });

		// $(document).on('click', '#getForm', updateFeeInfo);

		$(function() {
			updateFeeInfo();
		});
	</script>
@stop