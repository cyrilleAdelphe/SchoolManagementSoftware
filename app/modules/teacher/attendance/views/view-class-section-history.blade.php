@extends('teacher.attendance.views.tabs')

@section('custom-css')
<link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('tab-content')
      <!-- Content Wrapper. Contains page content -->
      
              
              
                        <div class="row">
                          <div class="col-sm-3">
                            <div class="form-group">
                              <label >Choose Date</label>
                              <div class="input-group">                              
                                <span class="input-group-addon">
                                  <i class="fa fa-calendar"></i>
                                </span>
                                <input id="date" class="form-control" type="text" placeholder="Choose date range">
                              </div>
                            </div>
                          </div>
                          
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
                          

                        </div><!-- row ends -->
                        <div id = "class_section_history">
                          <p>Please select date, session, class and section</p>
                        </div>
                        <input type="hidden" id="teacher_id" value="{{ $teacher_id}}">


<input type="hidden" id="class_ajax" value="{{URL::route('ajax-get-teacher-classes')}}" />
                      
<input type="hidden" id="section_ajax" value="{{URL::route('ajax-get-classes-section-from-teacher-id')}}" />


@stop


@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}"></script>

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script type="text/javascript">
      $('#date').daterangepicker({

      }, function(start, end, label) {
        console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
      });
  /**
   * Update the table showing subjects corresponding to the selected class and section
  **/

  $(function() {
    var default_class = "{{ Input::get('class_id', 0) }}";
    var default_section = "{{ Input::get('section_id', 0) }}";

    if($('#academic_session_id').val() != 0)
    {
      updateClassList(default_class);
      updateSectionList();
    }



    $(document).on('change', '#academic_session_id', updateClassList);

     $("#class_id").change(updateSectionList);
    
      $("#section_id").change(function(e)
        {
          var date_range = $('#date').val();
          var class_id = $('#class_id').val();
          var section_id = $('#section_id').val();
          var academic_session_id = $('#academic_session_id').val();
          $('#class_section_history').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
          $.ajax( {
                        "url": "{{URL::route('teacher-ajax-get-class-section-history')}}",
                        "data": {"date_range" : date_range, "class_id" : class_id, "section_id" : section_id, "academic_session_id" : academic_session_id},
                        "method": "GET"
                        } ).done(function(data) {
                    $('#class_section_history').html(data);
                  });
        });

  });
</script>
@stop