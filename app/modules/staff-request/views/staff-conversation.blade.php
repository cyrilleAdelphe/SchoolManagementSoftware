@extends('backend.'.$role.'.main')

@section('custom-css')
<link href="{{asset('sms/assets/css/message.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
                  <div class="row mHead">
                    <div class="col-sm-3">
                      <div class="msg-sender">
                        <a href="#" class="text-green">{{Input::get('sender_name')}} ({{Input::get('sender_username')}})</a>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="msg-sender">
                        <a href="#" class="text-green">{{Input::get('reciever_name')}} ({{Input::get('reciever_username')}})</a>
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

@stop
