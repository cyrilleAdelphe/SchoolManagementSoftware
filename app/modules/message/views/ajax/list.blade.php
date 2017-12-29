<div class = "row">
  <div class = "col-md-3">
    Superadmin : {{$message_count['superadmin']}}
  </div>
  <div class = "col-md-3">
    Staff : {{$message_count['admin']}}
  </div>
  <div class = "col-md-3">
    Student : {{$message_count['student']}}
  </div>
  <div class = "col-md-3">
    Guardian : {{$message_count['guardian']}}
  </div>
</div>
@if(count($data))

                            <table id="pageList" class="table table-bordered table-striped">
                              <thead>
                                <tr>
                                  <th>SN.</th>
                                  <th>From</th>
                                  <th>Activities</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                              @define $i = 1
                              @foreach($data as $d)
                                <tr>
                                  <td>{{$i++}}</td>
                                  <td>{{$d->sender_name}} @if($d->new_message_count)<span class="label label-danger pull-right">{{$d->new_message_count}} <br/>new</span>@endif</td>
                                  <td>
                                    Last <span class="text-red">received</span> at {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->last_activity)->format('l jS F Y h:i:s A')}}
                                  </td>
                                  <td>
                                    <a data-toggle="tooltip" title="View" class="btn btn-info btn-flat" href="{{URL::route('message-view', array($message_from, $d->sender_id))}}?sender_name={{urlencode($d->sender_name)}}&sender_username={{urlencode($d->sender_username)}}">
                                      <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>

                            <div class = "container">
								<div class = 'paginate'>
									@if($data)
										{{$paginate}}
									@endif
								</div>
							</div>
	@else
	<div class="alert alert-warning alert-dismissable">
						<h4>
							<i class="icon fa fa-warning"></i>No Data Found
						</h4>
					</div>
	@endif