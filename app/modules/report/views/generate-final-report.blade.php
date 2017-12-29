@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Progress Reports</h1>
@stop
      

@section('content')


<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li><a href="{{URL::route('report-list')}}">Reports list</a></li>
    <li class = "active"><a href="#tab_2">Generate Final Report</a></li>
  </ul>
  <div class="content">
    <div class="row">
      <form method = "post" action = "{{URL::route('report-generate-final-report-post')}}">
      <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select session</label>
              {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id')}}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select exam</label>
              <select class="form-control" id = "exam_id" name = "exam_id">
                <option>-- Please select session first --</option>
              </select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select class</label>
              {{HelperController::generateSelectList('Classes', 'class_name', 'id', 'class_id')}}
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select section</label>
              <select class="form-control" id = "section_id" name = "section_id">
                <option>-- Please select class first --</option>
              </select>
            </div>
          </div>
      </div><!-- row ends -->
      <div class="row">
        <div class="col-sm-3">
          <button type = "submit" class="btn btn-block btn-success btn-lg" @if(!AccessController::checkPermission('report', 'can_config')) disabled @endif>
            <i class="fa fa-fw fa-bar-chart" ></i> Generate Report
          </button>
        </div>
      </div>   
      {{Form::token()}}
      </form>
    </div>

    <div class = "row">
      <div id = "ajax-content">
      </div>
    </div>
  </div>
</div>

<input type = "hidden" id = "ajax_get_exam_ids_from_session_id" value = "{{URL::route('ajax-get-exam-ids-from-session-id')}}">
<input type = "hidden" id = "ajax_get_section_ids_from_class_id" value = "{{URL::route('ajax-get-section-ids-from-class-id')}}">
<input type = "hidden" id = "ajax_get_students_from_session_id_and_student_id" value = "{{URL::route('ajax-get-students-from-session-id-and-student-id')}}">
<input type = "hidden" id = "repport_single_url" value = "{{URL::route('report-single')}}">

<input type="hidden" name= "default_class" id="default_class" value="{{ Input::get('class_id', 0) }}" />
<input type="hidden" name= "default_section" id="default_section" value="{{ Input::get('section_id', 0) }}" />

@stop

@section('custom-js')
<script>

  function updateLedgerClassList()
  {
    var exam_id = $('#ledger_exam_id').val();
    if (exam_id == 0) return;
    $('#ledger_class_id').html('<option value="0">Loading...</option>');
    $.ajax( {
                      "url": "{{URL::route('ajax-get-class-ids-from-exam-id')}}",
                      "data": {"exam_id" : exam_id},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#ledger_class_id').html(data);
                });
  }

  function updateClassList(exam_id, default_class)
  {
    if (typeof(exam_id) == 'undefined') {
      exam_id = $('#exam_id_list').val();
    }

    if (exam_id == 0) return;
    $('#class_id_list').html('<option value="0">Loading...</option>');
    $.ajax( {
                      "url": "{{URL::route('ajax-get-class-ids-from-exam-id')}}",
                      "data": {"exam_id" : exam_id},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#class_id_list').html(data);
                  if (typeof(default_class) != 'undefined')
                  {
                    $('#class_id_list').val(default_class);
                  }
                });
  }

function updateExamID()
{
  var session_id = $('#session_id').val();
  if(!session_id || session_id==0) return;
  var url = $('#ajax_get_exam_ids_from_session_id').val();
  $('#exam_id').html('<option value="0">Loading...</option>');
  $.ajax( {
                    "url": url,
                    "data": {"session_id" : session_id, "field_name" : "exam_id_list"},
                    "method": "GET"
                    } ).done(function(data) {
                $('#exam_id').html(data);
              });
}

function updateExamIDList(default_exam)
{
  var session_id = $('#session_id_list').val();
  if(!session_id || session_id==0) return;
  var url = $('#ajax_get_exam_ids_from_session_id').val();
  $('#exam_id_list').html('<option value="0">Loading...</option>');
  $.ajax( {
                    "url": url,
                    "data": {"session_id" : session_id, "field_name" : "exam_id_list"},
                    "method": "GET"
                    } ).done(function(data) {
                $('#exam_id_list').html(data);
                if (typeof(default_exam) != 'undefined') {
                  $('#exam_id_list').val(default_exam);
                }
              });
}

            
function updateExamIDList2()
{
  var session_id = $('#session_id_search').val();
  if(!session_id || session_id==0) return;
  var url = $('#ajax_get_exam_ids_from_session_id').val();
  $('#exam_id_list2').html('<option value="0">Loading...</option>');
  $.ajax( {
                    "url": url,
                    "data": {"session_id" : session_id, "field_name" : "exam_id_list"},
                    "method": "GET"
                    } ).done(function(data) {
                $('#exam_id_list2').html(data);
              });
}

function updateLedgerExamId()
{
  var session_id = $('#ledger_session_id').val();
  if(!session_id || session_id==0) return;
  var url = $('#ajax_get_exam_ids_from_session_id').val();
  $('#ledger_exam_id').html('<option value="0">Loading...</option>');
  $.ajax( {
                    "url": url,
                    "data": {"session_id" : session_id, "field_name" : "exam_id_list"},
                    "method": "GET"
                    } ).done(function(data) {
                $('#ledger_exam_id').html(data);
              });
}        


$(function()
{
  updateExamIDList({{ Input::get('exam_id_list', 0) }});
  updateExamIDList2();
  updateClassList({{ Input::get('exam_id_list', 0) }}, {{ Input::get('class_id_list', 0) }});
  
  updateLedgerExamId();
  updateLedgerClassList();

  $('#session_id').change(function()
  {
    updateExamID(); 
  });

  $('#ledger_session_id').change(function()
  {
    updateLedgerExamId();
    updateLedgerClassList();
  });

  $('#ledger_exam_id').change(function()
  {
    updateLedgerClassList(); 
  });

  $('#exam_id').change(function()
  {
    var exam_id = $('#exam_id').val();
     var url = "{{URL::route('ajax-get-class-ids-from-exam-id')}}";
     $('#class_id').html('<option value="0">Loading...</option>');
     $.ajax( {
                    "url": url,
                    "data": {"exam_id" : exam_id},
                    "method": "GET"
                    } ).done(function(data) {
                $('#class_id').html(data);
              });
  });

  $('#session_id_list').change(function()
  {
    updateExamIDList();
  });

  $('#exam_id_list').change(function()
    {
      updateClassList();
      // var exam_id = $(this).val();
      
      //   $.ajax( {
      //                 "url": "{{URL::route('ajax-get-class-ids-from-exam-id')}}",
      //                 "data": {"exam_id" : exam_id},
      //                 "method": "GET"
      //                 } ).done(function(data) {
      //             $('#class_id_list').html(data);
      //           });   
      });

  $('#class_id_list').change(function()
  {

    var class_id_list = $('#class_id_list').val();
    var session_id_list = $('#session_id_list').val();
    var exam_id_list = $('#exam_id_list').val();
    var current_url = $('#current_url').val();
    current_url += '?class_id_list=' + class_id_list + '&session_id_list=' + session_id_list + '&exam_id_list=' + exam_id_list;
    window.location.replace(current_url);

  });

  $('#class_id').change(function()
  {

    var class_id = $(this).val();
    var url = $('#ajax_get_section_ids_from_class_id').val();
    $('#section_id').html('<option value="0">Loading...</option>');
    $.ajax( {
                      "url": url,
                      "data": {"class_id" : class_id},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#section_id').html(data);
                });


  });

  $('#section_id').change(function()
  {
    var session_id = $('#session_id').val();
    var class_id = $('#class_id').val();
    var section_id = $('#section_id').val();
    var exam_id = $('#exam_id').val();

    $('#ajax-content').html('loading.......');
    $.ajax(
    {
      url : '{{URL::route('api-report-final-class-report')}}',
      method : 'GET',
      data : {'exam_id' : exam_id, 'session_id' : session_id, 'class_id' : class_id, 'section_id' : section_id}
    }).done(function(data)
    {
      $('#ajax-content').html(data);
    });
  });
});
</script>
@stop