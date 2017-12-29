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
        <td>{{$d->name}} ({{$d->username}})</td>
        <td>
          Last <span class="text-red">sent </span> at {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $d->last_activity)->format('l jS F Y h:i:s A')}}
        </td>
        <td>
          <a data-toggle="tooltip" title="View" class="btn btn-info btn-flat" href="{{URL::route('staff-request-staff-conversation', array($d->message_from_group, $d->message_from_id, $d->message_to_group, $d->message_to_id))}}?sender_name={{Input::get('sender_name')}}&sender_username={{Input::get('sender_username')}}&reciever_name={{$d->name}}&reciever_username={{$d->username}}">
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
        {{$data->appends(Input::query())->links()}}
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