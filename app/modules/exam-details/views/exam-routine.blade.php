@extends('exam-details.views.tabs')

@section('tab-content')
                        
<div class="row">
<div class="col-sm-3">
  <div class="form-group">
  <?php $current_session = AcademicSession::where('is_current','yes')->first()['id']; ?>
 
    <label>Select Session</label>
    {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', $selected = Input::old('academic_session_id') ? Input::old('academic_session_id') : $current_session)}}
    <span class = "form-error">@if($errors->has('exam_id')) {{ $errors->first('exam_id') }} @endif</span>
  </div>
</div>
<div class="col-sm-3">
  <div class="form-group">
    <label>Select exam</label>
    {{HelperController::generateSelectList('ExamConfiguration', 
                                          'exam_name', 
                                          'id', 
                                          'exam_id', 
                                          Input::has('exam_id')?Input::get('exam_id'):'', array(array('field' => 'session_id', 'operator' => '=', 'value' => Input::get('academic_session_id', $current_session))), true)}}
    <span class = "form-error">@if($errors->has('exam_id')) {{ $errors->first('exam_id') }} @endif</span>
  </div>
</div>

<div class="col-sm-3">
  <div class = "form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
    <label>Select class</label>
    <select name="class_id" id="class_id" class="form-control">
      <option value="0">--Select Exam First--</option>
    </select>
    <span class = "form-error">@if($errors->has('class_id')) {{ $errors->first('class_id') }} @endif</span>
  </div>
</div>

<div class="col-sm-3">
  <div class="form-group @if($errors->has("section_id")) {{"has-error"}} @endif">
    <label>Select section</label>
      
      @if(Input::has('section_id'))  
      {{HelperController::generateSelectList('Section', 'section_code', 'id', 'section_id', $selected = Input::has('section_id')?Input::get('section_id'):'')}}
      @else
      <select name="section_id" id="section_id" class="form-control">
      <option value="0">--Select Class First--</option>
      </select>
      @endif
      <span class = "form-error">@if($errors->has('section_id')) {{ $errors->first('section_id') }} @endif</span>
  </div>
</div>

<input type = "hidden" id = "ajax-get-exam-ids-from-session-id" value = "{{URL::route('ajax-get-exam-ids-from-session-id')}}">

</div>

<div class="row">
<div class="col-sm-12">
@if(count($last_updated_data))
<h4 class="text-red">
  @if(Input::get('class_id', 0)) 
    Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}} 
    @endif
    <small>Last updated by : {{$last_updated_data->updated_by}} at <span class="text-green">{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $last_updated_data->updated_at)->format('d M Y, g:i A')}}</span></small></h4>
@endif

<table id="pageList" class="table table-bordered table-striped">
  
  @if(count($data))
  <thead>
    <tr>
      <th>SN</th>
      <th>Subject</th>
      <th>Date</th>
      <th>Day</th>
      <th>Duration</th>
      <th>Comments</th>
      <!-- <th>Actions</th> -->
    </tr>
  </thead>
  <tbody>
    @define $i = 1
    @foreach($data as $d)
    <tr>
      <td>{{$i++}}</td>
      <td>{{$required_subjects[$d->subject_id]}}</td>
      <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->start_date_in_ad)->format('d')}} {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->start_date_in_ad)->format('M')}}<br/><span class="text-red">{{$d->start_date_in_bs}}</span></td>
      <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->start_date_in_ad)->format('D')}}</td>
      <td>{{$d->duration}}</td>
      <td>{{$d->remarks}}</td>

        </button> 
     
    </tr>
    @endforeach
    
  </tbody>
@else
  <div class="alert alert-warning alert-dismissable">
    <h4><i class="icon fa fa-warning"></i>No Data Found</h4>
  </div>
@endif
    </table>
  </div>
</div>
                    

@stop

@section('custom-js')

<script>
  function updateClassList(exam_id, default_class) {
    if(typeof(exam_id) == 'undefined')
      var exam_id = $('#exam_id').val();

    if(exam_id == 0) return;
      
    $.ajax( {
                  "url": "{{URL::route('ajax-get-class-ids-from-exam-id')}}",
                  "data": {"exam_id" : exam_id},
                  "method": "GET"
                  } ).done(function(data) {
              $('#class_id').html(data);
              if (typeof(default_class) != 'undefined') {
                $('#class_id').val(default_class);
              }
            }); 
  }

  function updateSectionList(class_id, default_section) {
    if(typeof(class_id) == 'undefined')
      class_id = $('#class_id').val();
      
    $.ajax( {
                  "url": "{{URL::route('exam-details-get-section-ids')}}",
                  "data": {"class_id" : class_id},
                  "method": "GET"
                  } ).done(function(data) {
              $('#section_id').html(data);
              if (typeof(default_section) != 'undefined') {
                $('#section_id').val(default_section);
              }

            });   
  }

  function updateExamList() {
    
      session_id = $('#academic_session_id').val();
      
    $.ajax( {
                  "url": "{{URL::route('ajax-get-exam-ids-from-session-id')}}",
                  "data": {"session_id" : session_id},
                  "method": "GET"
                  } ).done(function(data) {
              $('#exam_id').html(data);
            });   
  }
  
  $(function() {
    // var default_exam = /exam_id=([^&]+)/.exec(location.search);
    // default_exam = default_exam ? default_exam[1] : undefined;

    // var default_class = /class_id=([^&]+)/.exec(location.search);
    // default_class = default_class ? default_class[1] : undefined;

    var default_exam = "{{ Input::get('exam_id', 0) }}";
    var default_class = "{{ Input::get('class_id', 0) }}";
    var default_section = "{{ Input::get('section_id', 0) }}";


    updateClassList(default_exam, default_class);
    updateSectionList(default_class, default_section);

    $('#academic_session_id').change(function(e)
    {
      updateExamList();
    });
    
    $('#exam_id').change(function()
    {
        updateClassList();
    });
    

    $('#class_id').change(function()
    {
      updateSectionList();  
    });


    $('#section_id').change(function()
    {
      //write code to get subjects
      var class_id = $('#class_id').val();
      var exam_id = $('#exam_id').val();
      var section_id = $(this).val();
      var current_url = $('#current_url').val();
      current_url += '?class_id=' + class_id + '&section_id=' + section_id + '&exam_id=' + exam_id;
      window.location.replace(current_url);
    });

  });
</script>
@stop