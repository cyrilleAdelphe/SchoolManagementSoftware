@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>
    Update payments
  </h1>
@stop

@section('content')
<div class="row">
 <div class="col-sm-12 backBtn" style="margin-bottom:15px">
      <a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
  </div>
</div>   
<div class="row">
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
        @define $months = array('Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra');
      @else
        @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
        @define $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')
      @endif


      <select class="form-control" name="month" id="month">
      @foreach($months as $key=>$month_name)
      	@define $month_id = $key+1
      	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month_name}}</option>
      @endforeach
      </select>
    </div>
  </div>
</div><!-- row ends -->
<input type="hidden" id="query_url" value="{{URL::route('fee-update-payment-form-get')}}" />
<input type="hidden" id="_token" value="{{csrf_token()}}" />
{{-- <button id="getForm" class="btn btn-success" type="submit">Search</button> --}}
<div id="updateForm">
</div>
    
@stop

@section('custom-js')
	<script>
		function updateForm() {
			if(!$('#student_id').val() || !$('#month').val())
			{
				return;
			}
			$('#updateForm').html('<div class="dloading">Loading...<br/><img src="{{asset('sms/assets/img/loading.gif')}}" /></div><br/><br/>');
			$.get($('#query_url').val(),
							{
								'student_id':$('#student_id').val(),
								'month':$('#month').val(),
								'_token':$('#_token').val()
							},
							function(data, status) {
								if(status) {
									$('#updateForm').html(data);	
								}
							}
						);
		}

		$(document).on('change', '#month', function() {
      updateForm();
    });

    $(document).on('change', '#student_id', function() {
      updateForm();
    });

		// $(document).on('click', '#getForm', updateForm);

		$(function() {
			updateForm();
		});
	</script>
@stop