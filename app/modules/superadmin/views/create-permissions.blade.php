@extends('backend.superadmin.main')

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
@stop

@section('content')

<div class = 'container'>
	<form method = "post" action = "{{URL::route('permissions-create-post')}}"  class = "form-horizontal" id = "AdminForm">
		<div class = 'form-group'>
			<label for = 'module_id'  class = 'control-label col-xs-2'>Module Name :</label>
			<div>
				{{$modules}}
			</div>
		</div>
		<table>
			<thead>
				<tr>
					<th>SN</th>
					<th>Group Name</th>
					<th>View</th>
					<th>Add</th>
					<th>Edit</th>
					<th>Delete</th>
					<th>Purge</th>
				</tr>
			</thead>
			<tbody>
			<?php $i = 0; ?>
			@foreach($groups as $g)
			
				<tr>
					<td>{{++$i}}</td>
					<td><input type = 'hidden' name = 'group_id[]' value = "{{$g->id}}" >{{$g->group_name}}</td>

					@foreach($permissions as $p)
						<td><input type = 'checkbox' name = '{{$p}}{{$g->id}}' value = "yes" @if(isset($existingPermissions[$g->id]) && $existingPermissions[$g->id]->$p =='yes') 
																						{{'checked'}} 
																					@endif></td>
					@endforeach
				</tr>
			@endforeach
			</tbody>
		</table>
		{{Form::token()}}
		<input type = "hidden" name = "is_active" value = "yes">
		<input type = "submit" value = "create" class = "form-control">
	</form>
	<input type = "hidden" id = "current_url" value = "{{URL::current()}}">
</div>

<?php echo  File::get(app_path().'/modules/superadmin/scripts/permission-create-script.js'); ?>
@stop
