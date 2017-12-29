<table id="pageList" class="table table-bordered table-striped">
	<thead>
		<tr>
      <th>SN</th>
      <th>Event</th>
      <th>Remark / Position</th>
      <th>Action</th>
    </tr>
  </thead>
  @define $i = 1
  <tbody>
  	@foreach($extra_activities as $activity)
    <tr>
			<td>{{ $i++ }}</td>
			<td>{{ $activity->title }}</td>
			<td>{{ $activity->remarks }}</td>
			<td>
				<a data-toggle="tooltip" title="View detail" href = "{{ URL::route('events-view', $activity->event_id) }}" class="btn btn-info btn-flat" @if(!AccessController::checkPermission('extra-activity', 'can_view')) disabled @endif>
					<i class="fa fa-fw fa-eye"></i>
				</a>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
		