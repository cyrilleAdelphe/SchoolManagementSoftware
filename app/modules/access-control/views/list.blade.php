@extends('backend.'.$current_user->role.'.main')

@section('content')

<div class = 'content'>

	<div class="table-responsive">
		
		<table class = 'table table-striped table-hover table-bordered'>
			
				<tbody class = 'search-table'>
				@if(count($modules))
					<?php $i = 1; ?>
					
						@foreach($modules as $d)
							<tr>
								<td>{{$i++}}</td>
								<td><a href = "{{URL::route('access-permissions', $d)}}">{{$d}}</a></td>
							</tr>
						@endforeach
				@else
							<tr>
								<td>No Modules Found</td>
							</tr>
				@endif
				</tbody>
				
		</table>

	</div>

</div>

@stop
