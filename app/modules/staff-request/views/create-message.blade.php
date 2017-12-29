@extends('staff-request.views.tabs')

@section('tab-content')
  <div class="box box-info">
    <div class="box-header">
      <i class="fa fa-envelope"></i>
      <h3 class="box-title">Staff Request</h3>
    </div>
    <div class="box-body">
      <form action="#" method="post">
        <div class="form-group">
          <label> Request Type </label>
          {{ HelperController::generateStaticSelectList([
              'leave' =>  'Leave Request',
              'requisition'   =>  'Requisition'
            ], 'request_type', '') }}
        </div>
        <div class="form-group">
          <input type="text" class="form-control"  id = "message_subject" name="message_subject" placeholder="Subject"/>
        </div>
        <div>
          <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name = "message"  id = "message" ></textarea>
        </div>
      </form>
    </div>
    <div class="box-footer clearfix">
      <button class="pull-right btn btn-default" id="sendEmail">Send <i class="fa fa-arrow-circle-right"></i></button>
      <div id="loadingIcon"></div>
    </div>
  </div>
  <!-- quick email ends -->
  
@stop

@section('custom-js')
<script>

$(function() {
  
  $('#sendEmail').click(function()
  {

    var message_from_group = '{{ $details_role }}';
    var message_from_id = '{{ $details_id }}';
    var message = $('#message').val();
    
    var message_subject = $('#message_subject').val();
    var token = '{{csrf_token()}}';

    $('#sendEmail').hide();
    $('#loadingIcon').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>Sending Request...</div>');
    
    $.ajax( {
    "url": "{{URL::route('staff-request-api-send')}}",
    "data": {
      "message_from_group" : message_from_group, 
      "message_from_id" : message_from_id, 
      "message" : message, 
      "message_subject" : message_subject, 
      "request_type" : $('#request_type').val(),
      "_token" : token, 
      'is_active' : 'yes'
    },
    //"dataType" : "json",
    "method": "POST",
    }).done(function(data) {
      data = $.parseJSON(data);
      console.log(data); //.status)
      if(data.status == 'success') {
        alert('Message sent');
        $('#message').val('');
        $('#message_subject').val('');
        
      }
      else
        alert(data.message);

      $('#sendEmail').show();
      $('#loadingIcon').html('');
    });

  });
  
})
</script>
@stop