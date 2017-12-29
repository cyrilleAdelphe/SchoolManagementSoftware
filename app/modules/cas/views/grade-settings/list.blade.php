@extends('backend.'.$role.'.main')

@section('page-header')
  <h1>Update Your Grade Settings</h1>
@stop

@section('content')
            
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class = "active"><a href="{{URL::route('cas-grade-settings-list')}}">Grading</a></li>
                  <li><a href="{{URL::route('cas-sub-topics-list')}}" >Sub Topics</a></li>
                  <li><a href="{{URL::route('remark-setting-list')}}">Remarks setting</a></li>
                </ul> 
                <div class="tab-content">
                  <form id = "dynamicForm" method = "post" action = "{{URL::route('cas-grade-settings-post')}}">
                    <div class="tab-pane active" id="tab_1">
                      <div class="row">
                        <div class="col-sm-2">
                          <input type = "hidden" id = "old_session" value = "{{Input::get('session', 0)}}">
                          <input type = "hidden" id = "old_from_class" value = "{{Input::get('from_class', 0)}}">
                          <input type = "hidden" id = "old_to_class" value = "{{Input::get('to_class', 0)}}">
                          <div class="form-group">
                            @define $sessions = AcademicSession::where('is_active', 'yes')->select('session_name', 'id', 'is_current')->get();
                            <label>Session</label>
                            <select id = "session" name = "session" class = "form-control">
                              @foreach($sessions as $s)
                              @if($s->is_current == 'yes')
                                @define $current_session = $s->id
                              @endif
                              <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                             <label>From</label>
                              <select id = "from_class" class = "form-control" name = "from_class">
                                @define $classes = Classes::where('academic_session_id', $current_session)->select('class_name', 'id')->get();
                                <option value = "0">-- Select Class --</option>
                                @foreach($classes as $class)
                                <option value = "{{$class->id}}"> {{$class->class_name}} </option>
                                @endforeach
                              </select>
                          </div>
                        </div>
                        @define $c = []
                        <div class="col-sm-2">
                          <div class="form-group">
                             <label>To</label>
                            <select id="to_class" class="form-control" name = "to_class">
                              <option value = "0">-- Select Class --</option>
                              @foreach($classes as $class)
                              <option value = "{{$class->id}}"> {{$class->class_name}} </option>
                              @define $c[] = $class->id
                              @endforeach
                            </select>
                          </div>
                        </div>
                        
                      </div><!-- row ends -->

                      <!-- show only when data display -->
                      <div id = "ajax-content">

                      </div>

                      <div class="item-title">Grade settings</div>
                      <div class="row">
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>From</label>
                            <input type = "number" class="form-control" name = "from[]"/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>To</label>
                            <input type = "number" class="form-control" name = "to[]"/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Grade</label>
                            <input class="form-control" name = "grade[]"/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Grade point</label>
                            <input type = "number" step=0.01 class="form-control" name='grade_point[]'/>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label style="color: #fff; display: block">Add</label>
                            <a href="#" class="btn btn-primary btn-flat add_field_button">Add more </a>
                          </div>
                        </div>
                      </div><!-- row ends -->
                      <div class="input_fields_wrap">
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-lg btn-flat btn-success">Submit</button>
                        </div>
                      </div>
                    </div><!-- tab 1 ends -->
                    {{Form::token()}}
                  </form>
                  
                </div>
              </div>
            
         
      
@stop

@section('custom-js')
    <script>
      
    </script>
    <script type="text/javascript">
      $(document).ready(function() 
      {
          var max_fields      = 10; //maximum input boxes allowed
          var wrapper         = $(".input_fields_wrap"); //Fields wrapper
          var add_button      = $(".add_field_button"); //Add button ID
          
          var x = 1; //initlal text box count
          $(add_button).click(function(e){ //on add input button click
              e.preventDefault();
              if(x < max_fields){ //max input box allowed
                  x++; //text box increment
                  $(wrapper).append('<div class="addedField"><div class="row"><div class="col-sm-2"><div class="form-group"><label>From</label><input class="form-control" name = "from[]"/></div></div><div class="col-sm-2"><div class="form-group"><label>To</label><input class="form-control" name = "to[]" /></div></div><div class="col-sm-2"><div class="form-group"><label>Grade</label><input class="form-control" name="grade[]"/></div></div><div class="col-sm-2"><div class="form-group"><label>Grade point</label><input class="form-control" name="grade_point[]"/></div></div><div class="col-sm-2"><div class="form-group"><label class="form-label" style="color: #fff; display: block">Remove</label><a href="#" class="remove_field btn btn-danger btn-flat">Remove</a></div></div></div></div>'); //add input box
              }
          });
          
          $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            
              e.preventDefault(); $(this).parent().parent().parent().remove(); x--;
          })

          $('#to_class').change(function()
          {
            disableEnable();
            if($('#to_class').val() > 0 && $('#from_class').val() > 0)
            {

              getAjaxContent();
            }
          });

          $('#from_class').change(function()
          {
            disableEnable();
            if($('#to_class').val() > 0 && $('#from_class').val() > 0)
            {
              getAjaxContent();
            }
          });

          if($('#old_session').val() != 0)
            $('#session').val($('#old_session').val());
          if($('#old_from_class').val() != 0)
            $('#from_class').val($('#old_from_class').val());
          if($('#old_to_class').val() != 0)
            $('#to_class').val($('#old_to_class').val());
          getAjaxContent();

          function getAjaxContent()
          {
            
            $('ajax-content').html('loading');
            $.ajax
            ({
              'method' : 'get',
              'url' : '{{URL::route("cas-api-grade-settings-list-from-class-to-class")}}',
              'data' : {'session' : $('#session').val(), 'from_class' : $('#from_class').val(), 'to_class' : $('#to_class').val()}
            }).done(function(data)
            {
              $('#ajax-content').html(data);
            });  
          }

          function disableEnable()
          {
            if($('#to_class').val() > 0 && $('#from_class').val() > 0)
            {
              $('#edit').attr('disabled', false)
              $('#delete').attr('disabled', false)
            }
            else
            {
              $('#edit').attr('disabled', true);
              $('#delete').attr('disabled', true);
            }
          }

          $(document).on('click', '.edit', function(e)
          {
            e.preventDefault();
            var current_element = $(this);
            var current_grade_settings_data = current_element.parent().parent().parent();

            current_grade_settings_data.find('.ajax-add-more').css('display', 'block');

            $(current_grade_settings_data.find('.edit')).each(function(index)
            {

              $(this).removeClass('btn-success');
              $(this).addClass('btn-default');
              $(this).removeClass('edit');
              $(this).addClass('save');
              $(this).text('Save');

            });

            
            
            
            var current_table = current_grade_settings_data.find('table');

            var data = current_table.find('.data');
            $(data).each(function(index, data)
            {

              $(this).find('.input-text').text('');
              $(this).find('.input-data').attr('type', 'text');
            });

            data = current_table.find('.ajax-actions');
            $(data).each(function(index, data)
            {
              
                $(this).html('<a href = "#" class = "ajax-remove-row btn btn-danger">Remove</a>')
            });

          });

          $(document).on('click', '.save', function(e)
          {
            e.preventDefault();
            var current_element = $(this);
            var current_grade_settings_data = current_element.parent().parent().parent();
            

            var json = new Object();
            var class_id = [];
            var from_percent = [];
            var to_percent = [];
            var grade = [];
            var grade_point = [];

            current_grade_settings_data.find('.input-data-class-id').each(function()
            {
                  class_id.push($(this).val());
            });

            current_grade_settings_data.find('.input-data-from-percent').each(function()
            {
                  from_percent.push($(this).val());
            });

            current_grade_settings_data.find('.input-data-to-percent').each(function()
            {
                  to_percent.push($(this).val());
            });

            current_grade_settings_data.find('.input-data-grade-point').each(function()
            {
                  grade_point.push($(this).val());
            });

            current_grade_settings_data.find('.input-data-grade').each(function()
            {
                  grade.push($(this).val());
            });

            json.class_id = class_id;
            json.from = from_percent;
            json.to = to_percent;
            json.grade = grade;
            json.grade_point = grade_point;
            json._token = $("input[name=_token]").val();
            json.session = $('#session').val();

            //var json_string = JSON.stringify(json);
            console.log(json);
            $.ajax({
              url : '{{URL::route("cas-grade-settings-edit-post")}}',
              data : json,
              method : 'POST'

            }).done(function(data)
            {
              if(data.status == 'error')
              {
                alert(data.msg);
              }
              window.location.href = '{{URL::current()}}?session=' + $('#session').val() + '&from_class=' + $('#from_class').val() + '&to_class=' + $('#to_class').val();
            });

          });

          $(document).on('click', '.ajax-remove-row', function(e)
            {
              e.preventDefault();
              $(this).parent().parent().remove();
            });
            $(document).on('click', '.ajax-add-more', function(e)
            {
              var current_grade_settings_data = $(this).parent().parent().parent();
              console.log(current_grade_settings_data);
              var current_table_body = current_grade_settings_data.find('tbody').append('<tr><td></td><td class = "data"><p class = "input-text"></p><input class = "input-data input-data-from-percent" type = "text" value = ""></td><td class = "data"><p class = "input-text"></p><input class = "input-data input-data-to-percent" type = "text" value = ""></td><td class = "data"><p class = "input-text"></p><input class = "input-data input-data-grade" type = "text" value = ""></td><td class = "data"><p class = "input-text"></p><input class = "input-data input-data-grade-point" type = "text" value = ""></td><td class = "ajax-actions"><a href = "#" class = "ajax-remove-row btn btn-danger">Remove</a></td></tr>');
            });

      });
    </script>
  @stop