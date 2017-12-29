@extends('message.views.tabs')

@section('tab-content')
      <div class="box box-info">
          <div class="box-header">
            <i class="fa fa-envelope"></i>
            <h3 class="box-title">Quick Message</h3>
            <!-- tools box -->
            <div class="pull-right box-tools">
              <a class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
            </div><!-- /. tools -->
          </div>
          <div class="box-body">
            <form action="#" method="post">
              <input type="hidden" id="message_to_group" name="message_to_group" value="superadmin" />
              <div class="form-group">
                <!-- <input type="text" class="form-control" id = "message_to_id" name="message_to_id" placeholder="Insert ID :"/> -->
                <input type="text" class="form-control" id = "message_to_username" name="message_to_username" placeholder="Insert Username :"/>
              </div>
              {{-- <div class="form-group">
                <input type="text" class="form-control"  id = "message_subject" name="message_subject" placeholder="Subject"/>
              </div>
              <div> --}}
                <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name = "message"  id = "message" ></textarea>
              </div>
            </form>
          </div>
          <div class="box-footer clearfix">
            <button class="pull-right btn btn-default" id="sendEmail">Send <i class="fa fa-arrow-circle-right"></i></button>
          </div>
        </div>
        <!-- quick email ends -->
        <!-- modal starts -->
        <div id="find-id" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ID Finder</h4>
              </div>
              <div class="modal-body">
                <div class="form-group ">
                  <label>To</label>
                  {{HelperController::generateStaticSelectList(array('student' => 'Student', 'guardian' => 'Guardian', 'admin' => 'School Employee', 'superadmin' => 'Superadmin'), 'find_group')}}
                </div>
                <div class="row">                  
                  <div class="col-sm-6">
                    <div class="form-group ">
                      <label>Class</label>
                      {{HelperController::generateStaticSelectList(HelperController::getCurrentSessionClassList(), 'find_class_id')}}
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label>Section</label>
                      <select class="form-control" id = "find_section_id">
                        <option>-- Select Class First --</option>
                      </select>
                  </div>                  
                </div> <!-- row ends -->
                <div class="row">
                <div class = "col-sm-12">
                    <button type = "button" class = "btn btn-info btn-flat" id = "find_search">
                    <i class="fa fa-search"></i> Search</button>
                  </div>
                </div>
                <div id = "find_result">
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

@stop


@section('custom-js')
<script>
$(function()
{
  $('#sendEmail').click(function()
  {

    var message_from_group = '{{$details_role}}';
    var message_from_id = '{{$details_id}}';
    var message = $('#message').val();
    
    var message_subject = '';//$('#message_subject').val();
    var message_to_group = $('#message_to_group').val();
    // var message_to_id = $('#message_to_id').val();
    var message_to_username = $('#message_to_username').val();
    var token = '{{csrf_token()}}';

     $.ajax( {
                      "url": "{{URL::route('message-api-send')}}",
                      "data": {"message_from_group" : message_from_group, "message_from_id" : message_from_id, "message" : message, "message_subject" : message_subject, "message_to_group" : message_to_group, "message_to_username" : message_to_username, "_token" : token, 'is_viewed' : 'no', 'is_active' : 'yes'},
                      //"dataType" : "json",
                      "method": "POST",
                      } ).done(function(data) {
                        data = $.parseJSON(data);
                        console.log(data); //.status)
                        if(data.status == 'success')
                          {
                            alert('Message sent');
                            $('#message').val('');
                            $('#message_subject').val('');
                            $('#message_to_group').val('');
                            $('#message_to_id').val('');
                            $('#message_to_username').val('');
                          }
                        else
                          alert(data.message);
                });

  });

  $('#find_class_id').change(function()
  {

    var class_id = $(this).val();
    $('#find_section_id').html('<option value="0">loading...</option>');

    $.ajax( {
                      "url": "{{URL::route('ajax-get-section-ids-from-class-id')}}",
                      "data": {"class_id" : class_id},
                      //"dataType" : "json",
                      "method": "GET",
                      } ).done(function(data) {
                        $('#find_section_id').html(data);
                });

  });

  $('#find_search').click(function()
  {
    var class_id = $('#find_class_id').val();
    var section_id = $('#find_section_id').val();
    var group = $('#find_group').val();

    $.ajax({
              "url" : "{{URL::route('ajax-get-dashboard-modal-search-list')}}",
              "data" : {'class_id' : class_id, 'section_id' : section_id, 'group' : group},
              "method" : "GET"
          } ).done(function(data) {
                        $('#find_result').html(data);
                });
  });

});
function findIdSelect(username) {
  var group = $('#findIdGroup').val();
  $('#message_to_group').val(group);
  $('#message_to_username').val(username);
  }
</script>
@stop