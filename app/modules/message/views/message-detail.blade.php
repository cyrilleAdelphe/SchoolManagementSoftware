@extends('backend.'.$role.'.main')

@section('custom-css')
<link href="{{asset('sms/assets/css/message.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
  <div class="row mHead">
    <div class="col-sm-3">
      <div class="msg-sender">
        <a href="#" class="text-green">{{$sender_name}} ({{$sender_username}})</a>
      </div>
    </div>

    
    @if($data)
    {{$data->appends(Input::query())->links('pagination.custom')}}
    @endif
    
    <div class="col-sm-2">
      <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
    </div>
  </div>
  @define $index = count($data)
  @for($i = $index-1; $i>-1; $i--)
  <div class="chat">
    <div class="mImg @if($data[$i]->message_to_id == $me_id) pull-right @else pull-left @endif">
      <img src= @if($data[$i]->message_to_id == $me_id)"{{$you_image}}" @else "{{$me_image}}" @endif class="img-circle img-responsive" alt="profile image" />
    </div>
    <div class= @if($data[$i]->message_to_id == $me_id) "you" @else "me" @endif>                    
          {{$data[$i]->message}}<div>
            <small class="pull-right">
            <i class="fa fa-fw fa-calendar"></i>{{$data[$i]->created_at->format('d M D')}}<i class="fa fa-fw fa-clock-o"></i>{{$data[$i]->created_at->format('H:i A')}}
            </small>
          </div>
    </div>
  </div>
  @endfor
  
  <div class="msgBox">
    <div class="form-group">
      <textarea class="form-control"  name = "message" id = "message" placeholder="Your message ..." rows="3"></textarea>
    </div>
    <div class="form-group">
      <input type = "hidden" name = "message_to_group" id = "message_to_group" value = "{{$group}}">
      <input type = "hidden" name = "message_from_group" id = "message_from_group"value = "{{$my_group}}">
      <input type = "hidden" name = "message_from_id" id = "message_from_id" value = "{{$my_id}}">
      <input type = "hidden" name = "message_to_id" id = "message_to_id" value = "{{$id}}">
      
      <input type = "hidden" name = "message_subject" id = "message_subject"value = "none">

      <button class="btn btn-success btn-lg btn-flat" type="submit" id = "sendEmail"><i class="fa fa-fw fa-arrow-circle-o-up"></i> Send</button>
    </div>
  </div>
            <div class = "container">
{{-- <div class = 'paginate'>
                                  @if($data)
                                    {{$data->appends(Input::query())->links()}}
                                  @endif
                                </div>
              </div>--}}

@stop

@section('custom-js')
<script>
$(function()
{
$('#sendEmail').click(function(e)
  {
    e.preventDefault();
    
    var message_from_group = $('#message_from_group').val();
    var message_from_id = $('#message_from_id').val();
    var message = $('#message').val();
    
    var message_subject = $('#message_subject').val();
    var message_to_group = $('#message_to_group').val();
    // var message_to_id = $('#message_to_id').val();
    var message_to_id = $('#message_to_id').val();
    var token = '{{csrf_token()}}';

     $.ajax( {
                      "url": "{{URL::route('message-api-send')}}",
                      "data": {"message_from_group" : message_from_group, "message_from_id" : message_from_id, "message" : message, "message_subject" : message_subject, "message_to_group" : message_to_group, "message_to_id" : message_to_id, "_token" : token, 'is_viewed' : 'no', 'is_active' : 'yes'},
                      //"dataType" : "json",
                      "method": "POST",
                      } ).done(function(data) {
                        data = $.parseJSON(data);
                        console.log(data); //.status)
                        if(data.status == 'success')
                          {
                            location.reload();
                          }
                        else
                          alert(data.message);
                });

  });
});

</script>
@stop