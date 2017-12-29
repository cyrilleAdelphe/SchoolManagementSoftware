<table id="pageList" class="table table-bordered table-striped">
	<thead>
		<tr>
      <th>SN</th>
    	<th>Date</th>
    	<th>Status</th>
    	<th>Comment</th>
    </tr>
  </thead>
  <tbody>
  	@define $i = 1
  	@foreach($attendance_records as $d)
  		<tr>
  			<td>{{ $i++ }}</td>
  			<td>{{ $d[3] }}</td>
  			<td>
          @if ($d[1] == 'p') Present
          @elseif ($d[1] == 'a') Absent
          @elseif ($d[1] == 'l') Late
          @endif
        </td>
  			<td>{{ $d[2] }}</td>
  		</tr>
  	@endforeach
  </tbody>
</td>