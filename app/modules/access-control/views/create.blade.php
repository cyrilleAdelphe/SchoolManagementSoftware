@extends('backend.'.$current_user->role.'.main')

@section('page-header')
	<h1>{{$module_name}}</h1>
@stop

@section('content')

<div class = 'content'>
@if(count($access))
		<form action = "{{URL::route('access-permissions-post', $module_name)}}" method = "post">
			<div class = "row">
				<div class = "col-md-3">Permission Type</div>
				<div class = "col-md-9">Permission Groups</div>
			</div>
			
			@foreach($access as $permission_type => $a)
				<div class = "row">
					<div class = "col-md-3">{{$a['alias']}}<input type = "hidden" name = "permission_type[]" value = "{{$permission_type}}"><input type = "hidden" name = "{{$permission_type}}_alias" value = "{{$a['alias']}}">
					@foreach($a['routes'] as $routes)
						<input type = "hidden" name = "{{$permission_type}}_routes[]" value = "{{$routes}}">
					@endforeach
					</div>
					<div class = "col-md-9">Admins
						<div class = "row">
							@foreach($groups as $group_id => $group_name)
								<span><input type = "checkbox" name = "{{$permission_type}}_admin[]" value = "{{$group_id}}" @if(in_array($group_id, $a['admin'])) checked @endif>{{$group_name}}</span>
							@endforeach
						</div>
						<div class = "row">
							<span><input type = "checkbox" name = "{{$permission_type}}_user[]" value = "student" @if(in_array('student', $a['users'])) checked @endif>Student</span><span><input type = "checkbox" name = "{{$permission_type}}_user[]" value = "guardian" @if(in_array('guardian', $a['users'])) checked @endif>Guardian</span>
						</div>
						<div class = "row">
							<span><input type = "checkbox" name = "{{$permission_type}}_frontend" value = "frontend" @if(isset($a['frontend']) && $a['frontend'] == 'yes')) checked @endif>Frontend</span>
						</div>
					</div>
				</div>
			@endforeach

			<input type = "submit" class = "btn btn-success" value = "Set Permission">
			
			{{Form::token()}}
		</form>
@endif
</div>

@stop
