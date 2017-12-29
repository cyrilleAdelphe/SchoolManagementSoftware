
@if(count($data))
  <script type="text/javascript">
  function approveRequest(id, element)
  {
    $.ajax({
      url: "{{URL::route('staff-request-api-approve')}}",
      data: {
        id: id,
        _token: '{{ csrf_token() }}'
      },
      method: 'POST'
    }).done(function(data) {
      data = $.parseJSON(data);
      alert(data.message);
      if (data.status == 'success') {
        window.location.reload();
      }
    });
  }
  </script>

  <table id="pageList" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>SN.</th>
        <th>From</th>
        <th>Type</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Approved</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    @define $i = 1
    @foreach($data as $d)
      <tr>
        <td>{{ $i++ }}</td>
        <td>{{ $d->name }}</td>
        <td>{{ $d->request_type }}</td>
        <td>{{ $d->message_subject }}</td>
        <td>{{ $d->message }}</td>
        <td class="requestStatus">{{ $d->is_approved }}</td>
        <td>
          <a data-toggle="tooltip" title="@if($d->is_approved == 'yes') Disapprove @else Approve @endif" class="btn btn-info btn-flat @if($d->is_approved == 'yes') bg-red @endif" onclick="approveRequest({{ $d->id }}, this)">
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