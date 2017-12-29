@extends('exam-details.views.tabs')

@section('custom-css')
  <!-- Daterange picker -->
  <link href="{{asset('sms/plugins/daterangepicker_exam/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
  
  <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<style type="text/css">
  #scroll-bar{
    padding: 20px;
    white-space: nowrap;
    overflow-x:auto; 

  }
</style>

  
@stop

@section('tab-content')
<form action = "{{URL::route($module_name.'-'.'create-and-edit')}}" method = "post">
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
          {{HelperController::generateSelectList('ExamConfiguration', 'exam_name', 'id', 'exam_id', Input::has('exam_id')?Input::get('exam_id'):'', array(array('field' => 'session_id', 'operator' => '=', 'value' => Input::get('academic_session_id', $current_session))), true)}}
          <span class = "form-error">@if($errors->has('exam_id')) {{ $errors->first('exam_id') }} @endif</span>
        </div>
      </div>
      
      <div class="col-sm-3">
        <div class = "form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
          <label>Select class</label>
          <select name="class_id" id="class_id" class="form-control">
            <option value = "0"> -- Select Exam First --</option>
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

    @if(count($data['last_updated_data']))
    <h4 class="text-red">Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}} <small>Last updated by : {{$data['last_updated_data']->updated_by}} at <span class="text-green">{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data['last_updated_data']->updated_at)->format('d M Y, g:i A')}}</span></small></h4>
    @endif
    @if(count($data['total_results_data']))
    <div class="row" >
      <div class="col-sm-12">
        <div class="form-group">
          <a href = "{{URL::route('delete-all-records-of-an-exam', array(Input::get('exam_id', 0)))}}" class = "btn btn-danger">Delete data from this exam</a>
        </div>
      </div>
    </div>
    <div class="row" id = "scroll-bar">
      <div class="col-sm-12">
        <table id="pageList" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>SN</th>
              <th>Subjects</th>
              <th>*Full marks</th>
              <th>*Pass marks</th>
              <th>*Practical Full marks</th>
              <th>*Practical Pass marks</th>
              <th>*Start Date</th>
              <th>*Duration</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            @define $i = 1
            @foreach($data['total_results_data'] as $index => $t)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$t['subject_name']}}<input type = "hidden" name = "subject_id[]" value = "{{$index}}"></td>
                <td><input type = "text" name = "full_marks[]" value = "@if(isset($data['data'][$index]['full_marks'])){{$data['data'][$index]['full_marks']}}@else{{$t['full_marks']}}@endif"></td>
                <td><input type = "text" name = "pass_marks[]" value = "@if(isset($data['data'][$index]['pass_marks'])){{$data['data'][$index]['pass_marks']}}@else{{$t['pass_marks']}}@endif"></td>
                <td><input type = "text" name = "practical_full_marks[]" value = "@if(isset($data['data'][$index]['practical_full_marks'])){{$data['data'][$index]['practical_full_marks']}}@else{{$t['practical_full_marks']}}@endif"></td>
                <td><input type = "text" name = "practical_pass_marks[]" value = "@if(isset($data['data'][$index]['practical_pass_marks'])){{$data['data'][$index]['practical_pass_marks']}}@else{{$t['practical_pass_marks']}}@endif"></td>
                <td>
                <input type = "text" 
                  class="datetimepicker" 
                  name = "start_date_in_ad[]" 
                  value = "{{
                    ($t['is_graded'] == 'no') ? '' : 
                    isset($data['data'][$index]['start_date_in_ad']) ? 
                      DateTime::createFromFormat(
                        'Y-m-d H:i:s', 
                        $data['data'][$index]['start_date_in_ad']
                      )->format('m/d/Y g:i A') : 
                      ''
                     }}"  
                    @if($t['is_graded'] == 'no') 
                      disabled 
                    @endif
                  >
                  
                  @if ($t['is_graded'] == 'no')
                    <input type = "hidden" 
                      name = "start_date_in_ad[]"
                      value = "{{ date('m/d/Y g:i A') }}"
                    >
                  @endif

                </td>
                <td>
                  <input type = "text" 
                    name = "duration[]" 
                    value = "{{
                      isset($data['data'][$index]['duration']) ? 
                        $data['data'][$index]['duration'] :
                        ''
                    }}"  
                    @if($t['is_graded'] == 'no') 
                      disabled 
                    @endif
                  >
                  @if($t['is_graded'] == 'no') 
                      <input type = "hidden"
                        name = "duration[]"
                        value = "N/A"
                      > 
                    @endif
                </td>
                <td><input type = "text" name = "remarks[]" value = "@if(isset($data['data'][$index]['remarks'])){{$data['data'][$index]['remarks']}}@else @endif"></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="form-group">
      <button class="btn btn-primary" type="submit">Submit</button>
    </div>
    @endif
  {{Form::token()}}
</form>

@stop

@section('custom-js')
<script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
  <script src="{{asset('sms/plugins/daterangepicker_exam/daterangepicker.js')}}" type="text/javascript"></script>
<!-- for datetime picker -->
<script>
  $(function() {
      $('.datetimepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: true, 
        timePickerIncrement: 5, 
        format: 'MM/DD/YYYY h:mm A'
      }, 
      function(start, end, label) {
        // place the code you want to fire when a datetime is selected
      });
  });
</script>
  
<script>
  function updateExamList() {
    
    session_id = $('#academic_session_id').val();

    if (session_id == 0) return;
    $('#exam_id').html('<option value="0">loading...</option>'); 
    $.ajax( {
                  "url": "{{URL::route('ajax-get-exam-ids-from-session-id')}}",
                  "data": {"session_id" : session_id},
                  "method": "GET"
                  } ).done(function(data) {
              $('#exam_id').html(data);
            });   
  }

  function updateClassList(exam_id, default_class) {
    if(typeof(exam_id) == 'undefined') {
      exam_id = $('#exam_id').val();  
    }
    
    if(exam_id == 0) return;
    
    $('#class_id').html('<option value="0">loading...</option>'); 
    
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
    if(typeof(class_id) == 'undefined') {
      class_id = $('#class_id').val();  
    }

    if(class_id==0) return;
    $('#section_id').html('<option value="0">loading...</option>'); 
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

  function updateSubjectList(exam_id, class_id, section_id) {
    //write code to get subjects
    if(typeof(exam_id) == 'undefined') {
      exam_id = $('#exam_id').val();  
    }
    if(typeof(class_id) == 'undefined') {
      class_id = $('#class_id').val();  
    }
    if(typeof(section_id) == 'undefined') {
      section_id = $('#section_id').val();  
    }

    if(!class_id || !section_id || !exam_id) return;
    
    var current_url = $('#current_url').val();
    current_url += '?class_id=' + class_id + '&section_id=' + section_id + '&exam_id=' + exam_id;
    window.location.replace(current_url);
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

    $('#exam_id').change(function() {
      updateClassList();
    });

    $('#class_id').change(function() {
      updateSectionList();
    });

    $('#section_id').change(function() {
      updateSubjectList();
    });

  });
</script>
@stop