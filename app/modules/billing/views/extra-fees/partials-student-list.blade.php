@define $status = false
<table class = "table table-responsive">
	<thead>
		<tr>
			
			<th>Roll</th>
			<th>Name</th>
			<th>Amount</th>
		</tr>
	</thead>
	<tbody>
		
		@foreach($data as $d)
		@define $status = true
		<tr>
			
			<td>{{$d->current_roll_number}}</td>
			<td>{{$d->student_name}}  {{$d->last_name}} ({{$d->username}})<input type = "hidden" name = "student_id[]" value = "{{$d->id}}"></td>
			<td><input name = "fee_amount[]" type = "number" step=0.01 value = @if(isset($existing_data[$d->id])) "{{$existing_data[$d->id]}}" @else "" @endif></td>
		</tr>
		@endforeach
	</tbody>
</table>

@if($status)
<input type = "submit" class = "btn btn-success" value = "Create">
@else
<h1>No Students Found</h1>
@endif