@extends('attendance.views.tabs')

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


<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />
                      
@stop


@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}"></script>

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<!-- remove code here <script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script> -->
<script type="text/javascript">
      $('#date').daterangepicker({

      }, function(start, end, label) {
        console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
      });
  /**
   * Update the table showing subjects corresponding to the selected class and section
  **/

  function updateClassList(session_id, default_class)
  {
    if (typeof(session_id) == 'undefined') {
      session_id = $('#academic_session_id').val();
    }
    if (session_id == 0) return;
    $('#class_id').html('<option value="0">Loading...</option>');
    $.ajax( {
                      "url": "{{URL::route('ajax-get-related-classes')}}",
                      "data": {"session_id" : session_id, 'default_class_id' : default_class},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#class_id').html(data);
                  if (typeof(default_class) != 'undefined')
                  {
                    $('#class_id').val(default_class);
                  }
                });
  }

  function updateSectionList(class_id, default_section) {
    var class_id = $('#class_id').val();
          $('#section_id').html('<option value="0">Loading...</option>');
          $.ajax( {
                        "url": "{{URL::route('ajax-get-related-sections')}}",
                        "data": {"class_id" : class_id},
                        "method": "GET"
                        } ).done(function(data) {
                  
                    $('#section_id').html(data);
                    if (typeof(default_section) != 'undefined') {
                      $('#section_id').val(default_section);
                    }
                  });
      
  }


  $(function() {
    var default_class = "{{ Input::get('class_id', 0) }}";
    var default_section = "{{ Input::get('section_id', 0) }}";

    if($('#academic_session_id').val() != 0)
    {
      updateClassList($('#academic_session_id').val(), default_class);
      updateSectionList($('#class_id').val(), default_section);
    }

    $(document).on('change', '#academic_session_id', updateClassList);
  
    $('#class_id').change(function()
      {
        updateSectionList($('#class_id').val());
      });
    
      $("#section_id").change(function(e)
        {
          var date_range = $('#date').val();
          var class_id = $('#class_id').val();
          var section_id = $('#section_id').val();
          var academic_session_id = $('#academic_session_id').val();
          $('#class_section_history').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
          $.ajax( {
                        "url": "{{URL::route('ajax-get-class-section-history')}}",
                        "data": {"date_range" : date_range, "class_id" : class_id, "section_id" : section_id, "academic_session_id" : academic_session_id},
                        "method": "GET"
                        } ).done(function(data) {
                    $('#class_section_history').html(data);
                  });
        });

  });
</script>
@stop