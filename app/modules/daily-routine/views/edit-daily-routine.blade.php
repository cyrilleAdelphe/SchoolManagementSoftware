@extends('backend.'.$role.'.main')

@section('custom-css')
    <!-- Theme style -->    
  <link href="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet"/> 
  <!-- For Autocomplete -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
@stop

@section('page-header')
  <h1>
    Class {{ $class_name }} {{ $section_code }}
    <small class="text-red">
      {{ Input::get('day') }}
    </small>
  </h1>
@stop

@section('content')

  <div class="row">
    <div class="col-sm-3 col-sm-offset-9" style="margin-bottom:15px">
      <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
    </div><!-- row ends -->
  </div>

  <form method = "post" action = "{{URL::route('daily-routine-edit-post')}}">
    <div class="row">
      <div class="col-sm-12">
        <div class="multi-field-wrapper">
          <div class="multi-fields">
            <table id="pageList" class="table table-bordered table-striped">
              <?php
                if (count($data) == 0)
                {
                  $data = array(
                    (object) array(
                      'subject' => '',
                      'teacher' => '',
                      'start_time'  => date('H:i:s'),
                      'end_time'    => date('H:i:s')  
                    )
                  );
                }
              ?>
              @if(count($data))  
              @endif
              
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Teacher</th>
                  <th>Start time</th>
                  <th>End time</th>
                </tr>
              </thead>
              <tbody>
                @define $i = 1
                @foreach($data as $d)
                <tbody class="multi-field">
                  <tr>
                    <td>
                      <div class="form-group ">
                        <input class="form-control" type="text" name = "subject[]" value="{{$d->subject}}"> 
                      </div>
                    </td>
                    <td>
                      <div class="form-group ">
                        <input class="form-control" type="text" name = "teacher[]" value="{{$d->teacher}}"> 
                      </div>
                    </td>
                    <td>
                      <div class="bootstrap-timepicker">
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control timepicker" name = "start_time[]" value="{{DateTime::createFromFormat('H:i:s', $d->start_time)->format('g:i A')}}"/>
                            <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                            </div>
                          </div><!-- /.input group -->
                        </div><!-- /.form group -->
                      </div>
                    </td>
                    <td>
                      <div class="bootstrap-timepicker">
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control timepicker" name = "end_time[]" value="{{DateTime::createFromFormat('H:i:s', $d->end_time)->format('g:i A')}}"/>
                            <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                            </div>
                          </div><!-- /.input group -->
                        </div><!-- /.form group -->
                      </div>
                    </td>
                    
                    <td>
                      <button type="button" class="remove-field btn btn-danger">Remove</button>
                    </td>
                  </tr>
                </tbody>
                @endforeach
              </tbody>  
            </table>
          </div>
          <button type="button" class="add-field btn form-group">Add field</button>
        </div>
      </div>
    </div>
    <br />
    <div class="form-group">
      <input type = "hidden" name = "session_id" value = "{{Input::get('session_id', 0)}}">
      <input type = "hidden" name = "class_id" value = "{{Input::get('class_id', 0)}}">
      <input type = "hidden" name = "day" value = "{{Input::get('day', '')}}">
      <input type = "hidden" name = "section_id" value = "{{Input::get('section_id', 0)}}">
      {{Form::token()}}
      <tr><td><button type="submit" class="btn btn-success" >Update</button></td></tr>
    </div> 
  </form>   

@stop

@section('custom-js') 
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/input-mask/jquery.inputmask.date.extensions.js')}}" type="text/javascript"></script>
    <script src="{{asset('sms/plugins/timepicker/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
      var subjects = {{json_encode($subjects)}};
      var teachers = {{json_encode($teachers)}};
      var subject_teacher_map = {{json_encode($subject_teacher_map)}};

      function autocompleteSubjects(context) {
        // Autocomplete subjects
        context.autocomplete({
          select: function( event , ui ) {
            // update teachers list when we select subject
            if (subject_teacher_map[ui.item.value]) {
              $(this).closest('tr').find('input[name^="teacher"]')
              .autocomplete('option', 'source', subject_teacher_map[ui.item.value]);
            }
          },
          source: subjects,
          minLength: 0
        }).focus(function () {
          $(this).autocomplete('search', $(this).val())
        }); 

        // if subject name cleared, show all teachers
        context.keyup(function() {
          if (!this.value) {  
            autocompleteTeachers($(this).closest('tr').find('input[name^="teacher"]'));
          }
        });
      }

      function autocompleteTeachers(context) {
        // Autocomplete teachers
        context.autocomplete({
          source: teachers,
          minLength: 0
        }).focus(function () {
          $(this).autocomplete('search', $(this).val())
        });
      }
      
      $(function () {
        // Timepicker
        $(".timepicker").timepicker({
          minuteStep: 5,
          showInputs: false
        }); 

        autocompleteSubjects($('input[name^="subject"]'));
        autocompleteTeachers($('input[name^="teacher"]'));
      });
    </script>

    <script src = "{{ Config::get('app.url') . 'app/modules/daily-routine/assets/js/dynamicAddRoutine.js' }}" type = "text/javascript"></script>

    
@stop

              