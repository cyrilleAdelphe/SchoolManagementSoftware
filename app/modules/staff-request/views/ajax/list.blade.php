@if(count($data))
  <table id="pageList" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>SN.</th>
        <th>Type</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Approved</th>
      </tr>
    </thead>
    <tbody>
    @define $i = 1
    @foreach($data as $d)
      <tr>
        <td>{{$i++}}</td>
        <td>{{$d->request_type}}</td>
        <td>{{$d->message_subject}}</td>
        <td>{{$d->message}}</td>
        <td>{{$d->is_approved}}</td>
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