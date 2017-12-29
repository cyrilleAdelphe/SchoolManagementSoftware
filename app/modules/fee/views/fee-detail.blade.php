@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>
    Fee Details
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
<input type="hidden" id="query_url" value="{{URL::route('fee-fee-detail-info-get')}}" />
<input type="hidden" id="_token" value="{{csrf_token()}}" />
{{-- <button id="getForm" class="btn btn-success" type="submit">Search</button> --}}

<br /><br />

<div id="feeInfo">
</div>

@stop

@section('custom-js')
<script>
  function updateFeeInfo()
  {
    if($('#academic_session_id').val()==0 || $('#month').val()==0)
    {
      return;
    }
    $('#feeInfo').html('<div class="dloading">Loading...<br/><img src="{{asset('sms/assets/img/loading.gif')}}" /></div><br/><br/>');
    $.get($('#query_url').val(),
          {
            'academic_session_id':$('#academic_session_id').val(),
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

  $(function() {
    $(document).on('change', '#academic_session_id', updateFeeInfo);
    $(document).on('change', '#month', updateFeeInfo);

    updateFeeInfo();
    // $(document).on('click', '#getForm', updateFeeInfo);
  });
</script>
@stop