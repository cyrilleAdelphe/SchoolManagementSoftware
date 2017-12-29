{{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}
@extends('backend.admin.main')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
     My Dashboard
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3>{{$data['no_of_upcoming_events']}}</h3>
            <p>Upcoming Events</p>
          </div>
          <div class="icon">
            <i class="ion ion-trophy"></i>
          </div>
        </div>
      </div><!-- ./col -->
      
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3>{{$data['no_of_teachers']}}</h3>
            <p>Teachers</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-stalker"></i>
          </div>
        </div>
      </div><!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
          <div class="inner">
            <h3>{{$data['no_of_total_students']}}</h3>
            <p>Total Students</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-contacts"></i>
          </div>
        </div>
      </div><!-- ./col -->
    </div><!-- /.row -->
    <div class="row">
      <section class="col-lg-6">              
        <div class="box box-primary"><!-- box upcoming events starts -->
          <div class="box-header">
            <i class="ion ion-clipboard"></i>
            <h3 class="box-title">Upcoming Events</h3>
          </div>
          
          @foreach($data['upcoming_events'] as $e)
          <div class="box-body">
              <ul class="todo-list">
                <li>
                  <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                  </span>
                  <span class="text"><a href="">{{$e->title}}</a></span>
                  <small class="label label-danger"><i class="fa fa-clock-o"></i>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $e->from_ad)->format('d M, H:i')}}</small>
                </li>
              </ul>
          </div>
          @endforeach

        </div><!-- box upcoming events ends -->      
        <div class="box box-warning"><!-- box upcoming events starts -->
          <div class="box-header">
            <i class="fa fa-fw fa-bullhorn"></i>
            <h3 class="box-title">{{$data['notice']->title}}</h3>
            <div class="box-body">
              <p>{{$data['notice']->body}}</p>
              <p>
                <a href="{{URL::route('delete-notice')}}" class="btn btn-danger btn-sm btn-flat" data-toggle="tooltip" title="Delete">
                  <i class="fa fa-trash"></i> Delete
                </a>
              </p>
            </div>
          </div>
        </div><!-- notice box ends -->   
        <div class="box box-info">
          <div class="box-header">
            <i class="fa fa-envelope"></i>
            <h3 class="box-title">Set Notice</h3>
          </div>
          <div class="box-body">
            <form action="#" method="post">
              
              <div class="form-group">
                <input type="text" class="form-control"  id = "notice_title" name="notice_title" placeholder="Title"/>
              </div>
              <div>
                <textarea class="myArea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name = "notice_body"  id = "notice_body" ></textarea>
              </div>
            </form>
          </div>
          <div class="box-footer clearfix">
            <button class="pull-right btn btn-default" id="sendNotice">Notify <i class="fa fa-arrow-circle-right"></i></button>
          </div>
        </div>

      </section>
      <section class="col-lg-6 connectedSortable ui-sortable">
        
         <!-- quick email widget -->

        <div class="box box-info">
          <div class="box-header">
            <i class="fa fa-envelope"></i>
            <h3 class="box-title">Quick Message</h3>
            <!-- tools box -->
            <div class="pull-right box-tools">
              <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
            </div><!-- /. tools -->
          </div>
          <div class="box-body">
            <form action="#" method="post">
              <input type="hidden" id="message_to_group" name="message_to_group" value="superadmin" />
              <div class="form-group">
                <!-- <input type="text" class="form-control" id = "message_to_id" name="message_to_id" placeholder="Insert ID :"/> -->
                <input type="text" class="form-control" id = "message_to_username" name="message_to_username" placeholder="Insert Username :"/>
              </div>
              <div class="form-group">
                <input type="text" class="form-control"  id = "message_subject" name="message_subject" placeholder="Subject"/>
              </div>
              <div>
                <textarea class="myArea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name = "message"  id = "message" ></textarea>
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
                <div>
                    <div class="form-group">
                      <button type = "button" class = "btn btn-info btn-flat" id = "find_search">
                      <i class="fa fa-fw fa-search"></i> Search</button>
                      <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                <div id = "find_result">
                </div>
              <div class="modal-footer">
                
              </div>
            </div>
          </div>
        </div>
        <!-- modal ends -->
      </section>
    </div><!-- row ends -->

  </section><!-- /.content -->
@stop        

@section('custom-js')
<script>
// copy the details of find id modal to quick message form
function findIdSelect(username) {
  var group = $('#findIdGroup').val();
  $('#message_to_group').val(group);
  $('#message_to_username').val(username);
}

$(function()
{
  
  $('#sendEmail').click(function()
  {

    var message_from_group = 'admin';
    var message_from_id = '{{Auth::admin()->user()->admin_details_id}}';
    var message = $('#message').val();
    
    var message_subject = $('#message_subject').val();
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

  $('#sendNotice').click(function()
  {

    var notice_title = $('#notice_title').val();
    var notice_body = $('#notice_body').val();
    var token = '{{csrf_token()}}';

     $.ajax( {
                      "url": "{{URL::route('api-post-notice')}}",
                      "data": {"notice_title" : notice_title, "notice_body" : notice_body, "_token" : token, 'is_viewed' : 'no', 'is_active' : 'yes'},
                      //"dataType" : "json",
                      "method": "POST",
                      } ).done(function(data) {
                        data = $.parseJSON(data);
                        
                        if(data.status == 'success')
                          {
                            alert(data.message);
                            $('#notice_title').val('');
                            $('#notice_body').val('');
                          }
                        else
                          alert(data.message);
                });

  });

  $('.mark_as_viewed').click(function(e)
  {
    e.preventDefault();
    var message_id = $(this).parent().find('.message_id').val();
    var token = '{{csrf_token()}}';

     $.ajax( {
                      "url": "{{URL::route('message-api-mark-viewed')}}",
                      "data": {"message_id" : message_id, '_token' : token},
                      //"dataType" : "json",
                      "method": "POST",
                      } ).done(function(data) {
                        data = $.parseJSON(data);
                        console.log(data.status)
                        if(data.status == 'success')
                        {
                          alert('Message Viewed'); //write code to change view
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
</script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- Bootstrap WYSIHTML5 -->
     <script src="{{asset('sms/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}" type="text/javascript"></script> 

    <!-- FastClick -->
     <script src="{{asset('sms/plugins/fastclick/fastclick.min.js')}}"></script> 
     <script type="text/javascript">
      $(function () {
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
      });
    </script>

@stop 
{{-- /////// AdminDashboard-v1-changed-made-here ///////// --}}