<div id="sendSMS{{$d->message_group_id}}" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmation</h4>
      </div>
      <div class="modal-body">
        <div class="row-fluid">
          <div class="col-md-9">
             Are you sure you want to send SMS?
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <form action="{{ URL::route('sms-send-post') }}" method="post">
              <input type="hidden" name="message_group_id" value="{{$d->message_group_id}}">
              <input type="hidden" name="user_group" value="{{ $d->user_group }}">
              <input type="hidden" name="subject" value="{{ $d->subject }}">
              <input type="hidden" name="message" value="{{ $d->message }}">
                            
              <button name="sendSMS{{$d->message_group_id}}" value="sendSMS{{$d->message_group_id}}" data-toggle="tooltip" title="Send SMS" class="btn btn-danger">
                <i class="fa fa-fw fa-eye"></i>
              </button>
              {{ Form::token() }}
          </form>
      </div>
     </div>
  </div>
</div>