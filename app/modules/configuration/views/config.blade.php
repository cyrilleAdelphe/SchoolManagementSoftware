@extends('backend.'.$current_user->role.'.main')

@section('content')

<div class = 'content'>
	@if($status == 'success')
		<form method = "post" action = "{{URL::route('show-configuration-post', $filename)}}"  class = "form-horizontal" id = "backendForm">
		
			<div class = 'form-group'>
				<h1>{{$content['header']}}</h1>
				<input type = "hidden" name = "header" value = "{{$content['header']}}">
			</div>
			@foreach($content['category_groups'] as $content_category => $c)
				<input type = "hidden" name = "display[]" value = "{{$c['display']}}">
				<input type = "hidden" name = "category_type_name[]" value = "{{$content_category}}">
				@if($c['display'] == 'yes')
					<div class = 'form-group'>
						<h2>{{$c['group_display_name']}}</h2>
					</div>
				@endif
				<input type = "hidden" name = "display_name[]" value = "{{$c['group_display_name']}}">

				@foreach($c['fields'] as $field)
					<div class = 'form-group'>
							<label for = 'coupon_description'  class = 'control-label'>{{$field['display_name']}} :</label>
							<input type = "hidden" name = "{{$content_category}}_display_name[]" value = "{{$field['display_name']}}">
							<input type = "hidden" name = "{{$content_category}}_field_name[]" value = "{{$field['field_name']}}">
							<input type = 'text' name = "{{$content_category}}_field_value[]" value= "{{$field['field_value']}}" class = 'form-control required'>
					</div>
				@endforeach
				
			@endforeach
			
			<input type = 'submit' value = 'submit'>
			<div class = 'form-row'>
				<div class='col-xs-offset-2 col-xs-10'>
				{{Form::token()}}
				</div>
			</div>
		</form>
	@else
		<h1>{{$msg}}</h1>
	@endif
</div>


@stop