@extends('backend.'.$role.'.main')
@section('custom-css')
  <link href="{{asset('sms/assets/css/print.css')}}" rel="stylesheet" type="text/css" />
@stop
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
      <label>Select class</label>
      <select name="class_id" id="class_id" class="form-control">
        <option value="0">--Select Session First--</option>
      </select>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label>Select section</label>
      <select name="section_id" id="section_id" class="form-control">
        <option value="0">--Select Class First--</option>
      </select>
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

<input type="hidden" id="query_url" value="{{URL::route('fee-fee-class-info-get')}}" />
<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />
<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />
<input type="hidden" id="default_class" value="{{Input::has('class_id')?Input::get('class_id'):0}}" />
<input type="hidden" id="_token" value="{{csrf_token()}}" />
{{-- <button id="getForm" class="btn btn-success" type="submit">Search</button> --}}

<br /><br />

<div id="classInfo">
</div>


@stop

@section('custom-js')
<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script>
  function updateClassInfo(class_id, section_id)
  {

    if(typeof(class_id)=='undefined')
    {
      class_id = $('#class_id').val();
    }

    if(typeof(section_id)=='undefined')
    {
      section_id = $('#section_id').val();
    }

    if($('#academic_session_id').val()==0 || class_id==0 || section_id==0)
    {
      return;
    }
    $('#classInfo').html('<div class="dloading">Loading...<br/><img src="{{asset('sms/assets/img/loading.gif')}}" /></div><br/><br/>');
    $.get($('#query_url').val(),
          {
            'academic_session_id':$('#academic_session_id').val(),
            'class_id':class_id,
            'section_id':section_id,
            'month':$('#month').val(),
            '_token':$('#_token').val()
          },
          function(data, status) {
            if(status) {
              $('#classInfo').html(data);
            }
          }
        );
  }

  $(document).on('change', '#academic_session_id', function() {
    updateClassList();
    updateClassInfo();
  });
  $(document).on('change', '#class_id', function() {
    updateSectionList();
    updateClassInfo();
  });
  $(document).on('change', '#section_id', function() {updateClassInfo()});
  $(document).on('change', '#month', function() {updateClassInfo()});

  

  $(function() {
    if($('#academic_session_id').val() != 0)
    {
      var default_class = /class_id=([^&]+)/.exec(location.search);
      default_class = default_class ? default_class[1] : undefined;

      var default_section = /section_id=([^&]+)/.exec(location.search);
      default_section = default_section ? default_section[1] : undefined;

      updateClassList(default_class);
      updateSectionList(default_section);
            
      updateClassInfo(default_class, default_section);
    }

    // $(document).on('click', '#getForm', function(){updateClassInfo()});
  });

</script>

@stop