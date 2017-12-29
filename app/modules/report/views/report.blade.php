@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Progress Reports</h1>
@stop
      

@section('content')


<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab_1" data-toggle="tab">Reports list</a></li>
    <li><a href="#tab_2" data-toggle="tab">Generate report</a></li>
    <li><a href="{{URL::route('report-generate-final-report-get')}}">Generate Final report</a></li>
    <li><a href="#tab_4" data-toggle="tab">Make Ledger</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab_1">
      <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Select session</label>
              {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id_list', Input::get('session_id_list', HelperController::getCurrentSession()))}}
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>Select exam</label>
              <select class="form-control" id = "exam_id_list">
              @if(Input::get('exam_id_list', 0))
                <option value = "0">-- select  --</option>
                <option value = "{{Input::get('exam_id_list', 0)}}" selected>{{HelperController::pluckFieldFromId('ExamConfiguration', 'exam_name', Input::get('exam_id_list', 0))}}</option>
              @else
                <option>-- select session first --</option>
              @endif
              </select>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>Select class</label>
              <select class="form-control" id = "class_id_list">
              @if(Input::get('class_id_list', 0))
                <option value = "0">-- select  --</option>
                <option value = "{{Input::get('class_id_list', 0)}}" selected>{{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id_list', 0))}}</option>
              @else
                <option>-- select Exam first --</option>
              @endif
              </select>
            </div>
          </div>
      </div><!-- row ends -->

      <div class="row">
        
        <div class="col-sm-4">
          <div class="form-group">
            <label>Select session</label>
            {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id_search', Input::get('session_id_search', HelperController::getCurrentSession()))}}
          </div>
        </div>

        <div class="col-sm-4">
          <label>View single student</label>
          <div class="input-group">
            <input class="form-control" id = "student_id_search" type="text" placeholder="Enter student's ID">
            <span class="input-group-btn">
            <button class="btn btn-info btn-flat" id = "student_search" type="button">Find</button>
            </span>
          </div>
        </div>

        <div id="ajax_student_search">
        </div>
      </div><!-- row ends -->   

      <div class="row">
        <div class="col-sm-12">
            @if(count($data))
              <h4 class="text-red">Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id_list', 0))}}</h4>
              <table id="pageList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>Section</th>
                    <th>Total students</th>
                    <th>Passed</th>
                    <th>Failed</th>
                    <th>Generated at</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                @define $i = 1;
                @foreach($data as $index => $d)
                  <tr>
                    <td>{{$i++}}</td>
                    <td>{{$d['section_code']}}</td>
                    <td>{{$d['total_students']}}</td>
                    <td>@if(isset($d['passed'])) {{$d['passed']}} @else 0 @endif</td>
                    <td>@if(isset($d['failed'])) {{$d['failed']}} @else 0 @endif</td>
                    <td>{{$d['updated_at']->format('d M Y g:i A')}}</td>
                    <td>
                      <a data-toggle="tooltip" title="View detail" href="{{URL::route('report-class')}}?session_id={{Input::get('session_id_list',0)}}&class_id={{Input::get('class_id_list',0)}}&exam_id={{Input::get('exam_id_list',0)}}&section_id={{$index}}" class="btn btn-info btn-flat" @if(!AccessController::checkPermission('report', 'can_view')) disabled @endif>
                        <i class="fa fa-fw fa-eye"></i>
                      </a>
                      <a title="Download PDF" href="{{URL::route('report-mass-print')}}?exam_id={{Input::get('exam_id_list',0)}}&class_id={{Input::get('class_id_list', 0)}}&section_id={{Input::get('section_id_list', $index)}}" class="btn btn-info btn-flat">
                        <i class="fa fa-fw fa-download"></i>
                      </a>
                      
                      <a href="#" data-toggle="modal" data-target="#deleteClassSection" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('report', 'can_delete') == false) disabled @endif>
                        <i class="fa fa-fw fa-trash"></i>
                      </a>
                      @include('report.views.modal-delete')
                    </td>
                  </tr>
                @endforeach
              @else
                <tr><td>No Reports Generated</td></tr>
              @endif
              
            </tbody>
          </table>
        </div>
      </div>                     
    </div><!-- tab 1 ends here -->


    <div class="tab-pane" id="tab_2">
      <form method = "post" action = "{{URL::route('report-generate-post')}}">
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
      <div class = "row">
        <div class = "col-sm-3">
          <input type = "checkbox" name = "print_type" value="two">Print Two in Single Page
        </div>
        <div class = "col-sm-3">
          <input type = "checkbox" name = "show_cas" value="yes" checked>Show CAS
        </div>

      </div>
      <div class="row">
        <div class="col-sm-3">
          <button id = "generate_report" type = "submit" class="btn btn-block btn-success btn-lg" @if(!AccessController::checkPermission('report', 'can_config')) disabled @endif>
            <i class="fa fa-fw fa-bar-chart" ></i> Generate Report
          </button>
        </div>
      </div>   
      {{Form::token()}}
      </form>
    </div>

    <div class="tab-pane" id="tab_4">
      <form method = "get" action = "{{URL::route('report-generate-ledger-get')}}">
      <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select session</label>
              {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'ledger_session_id', HelperController::getCurrentSession())}}
            </div>
          </div>
          
          <div class="col-sm-3">
            <div class="form-group">
              <label>Select exam</label>
              <select class="form-control" id = "ledger_exam_id" name = "exam_id">
                <option>-- Please select session first --</option>
              </select>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
              <label>Select Class</label>
              <select class="form-control" id = "ledger_class_id" name = "class_id">
                <option>-- Please select Exam first --</option>
              </select>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
              <button type = "submit" class = "btn btn-default form-control">Generate Ledger</button>
            </div>
          </div>
        </div>
      </form>
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
  enableDisableGenerateReport();
  updateExamIDList({{ Input::get('exam_id_list', 0) }});
  updateExamIDList2();
  updateClassList({{ Input::get('exam_id_list', 0) }}, {{ Input::get('class_id_list', 0) }});
  
  updateLedgerExamId();
  updateLedgerClassList();

  $('#session_id').change(function()
  {
    enableDisableGenerateReport();
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
    enableDisableGenerateReport();
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
    enableDisableGenerateReport();
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
    enableDisableGenerateReport();
  });

  $('#session_id_search').change(function()
  {
    updateExamIDList2();
  });

  $('#student_search').click(function()
  {

    var session_id = $('#session_id_search').val();
    var student_id = $('#student_id_search').val();

    var url = $('#ajax_get_students_from_session_id_and_student_id').val();
    $('#ajax_student_search').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
    $.ajax( {
              "url": url,
              //"contentType": "application/json",
              "data": {"session_id" : session_id, "student_id" : student_id},
              "method": "GET",
              //"dataType": "json"
              } ).done(function(data) 
              {
                console.log(data);
                data = $.parseJSON(data);
                if(data.status == 'success')
                {
                  var single_url = $('#repport_single_url').val();
                  var html_element = '';
                  html_element += '<table id="pageList" class="table table-bordered table-striped">'
                  html_element += '<thead>'
                  html_element += '<tr>'
                  html_element += '<th>SN</th>'
                  html_element += '<th>Student Name</th>'
                  html_element += '<th>Class</th>'
                  html_element += '<th>Section</th>'
                  html_element += '<th>Session</th>'
                  html_element += '<th>Exam</th>'
                  html_element += '<th>Action</th>'
                  html_element += '</tr>'
                  html_element += '</thead>'
                  html_element += '<tbody id = "ajax_student_search">'
                  html_element += '</tbody>'
                  html_element += '<tbody>'
                  var string = '';

                  $.each(data.data , function( index, value ) 
                  {
                     string += "<tr>";
                     string += "<td>" + (index + 1) + "</td>";
                     string += "<td>" + value.student_name + "</td>";
                     string += "<td>" + value.class_name + "</td>";
                     string += "<td>" + value.section_code + "</td>";
                     string += "<td>" + value.session_name + "</td>";
                     string += "<td>" + value.exam_name + "</td>";
                     string += '<td><a data-toggle="tooltip" title="View detail" href = "' + single_url + '?student_id=' + value.student_id + '&class_id=' + value.class_id + '&section_id=' + value.section_id + '&exam_id=' + value.exam_id +'" class="btn btn-info btn-flat"><i class="fa fa-fw fa-eye"></i></a></td>'; /* '<a data-toggle="tooltip" title="View detail" href="' + single_url + '?class_id=' + value.class_id + '&exam_id=' + value.exam_id + '&section_id='+ value.section_id + '&stduent_id='+ value.stduent_id + '" class="btn btn-info btn-flat">
                                  <i class="fa fa-fw fa-eye"></i>
                                </a>';
                    */
                     string += "</tr>";
                     html_element += string;
                     html_element += '</tbody>';

                  });
                }
                else
                {
                  html_element = '<table id="pageList" class="table table-bordered table-striped"><tbody><tr><td>' + data.data + '</td></tr></tbody></table>';
                }
                

                $('#ajax_student_search').html(html_element);
                  
              });



  });

  function enableDisableGenerateReport()
  {
    var session_id = parseInt($('#session_id').val());
    session_id = isNaN(session_id) ? 0 : session_id;
    var exam_id = parseInt($('#exam_id').val());
    exam_id = isNaN(exam_id) ? 0 : exam_id;
    var class_id = parseInt($('#class_id').val());
    class_id = isNaN(class_id) ? 0 : class_id;
    var section_id = parseInt($('#section_id').val());
    section_id = isNaN(section_id) ? 0 : section_id;

    if((session_id != 0) && (exam_id != 0) && (class_id != 0) && (section_id != 0))
    {
      $('#generate_report').prop('disabled', false);
    }
    else
    {
      $('#generate_report').prop('disabled', true); 
    }
  }


});
</script>
@stop