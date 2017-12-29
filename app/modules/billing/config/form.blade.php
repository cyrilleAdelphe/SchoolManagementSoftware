@extends('backend.'.$role.'.main')

@section('content')
@define $c = count($data)
	<form>

		@foreach($data as $index => $d)
			<div class = "form-group">
				<input type = "text" name = "fiscal_year[{{$count}}]" class = "form-control">
			</div>
			<div class = "form-group">
				<input type = "text" name = "fiscal_year[{{$count}}]" class = "form-control">
			</div>
		@endforeach
		{{Form::token()}}
	</form>

@stop