@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>Show Report Config</h1>
@stop

@section('content')
	<div class = "container">
		<a href = "{{ URL::route('student-report-get') }}" class = "btn btn-danger">Go To Report</a>
	</div>
	@define $chunk_data = array_chunk($data, 3);
	<form action = "{{ URL::route('student-show-report-config-post') }}" method = "post">
		<div class = "container">
			@foreach($chunk_data as $data)
			<div class = "row">
				@foreach($data as $d)
				<div class = "col-md-4">
					<input type = "checkbox" name = "show[{{ $d['id'] }}]" @if($d['show'] == 'yes') checked @endif value="yes">{{ $d['alias'] }}
					<input type = "hidden" name = "alias[{{ $d['id'] }}]" value = "{{$d['alias']}}">
					<input type = "hidden" name = "column_name[{{ $d['id'] }}]" value = "{{$d['column_name']}}">
					<input type = "hidden" name = "table[{{ $d['id'] }}]" value = "{{$d['table']}}">
					<input type = "hidden" name = "id[{{ $d['id'] }}]" value = "{{$d['id']}}">
					<input type = "hidden" name = "hidden[{{ $d['id'] }}]" value = "{{$d['hidden']}}">
				</div>
				@endforeach
			</div>
			@endforeach
			<div class = "row">
				<input type = "submit" class = "btn btn-success" value = "Set">
			</div>
		</div>
	</form>

@stop