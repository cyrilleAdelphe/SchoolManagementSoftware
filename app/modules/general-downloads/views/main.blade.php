@extends('backend.' . $role . '.main')

@section('content')
	<h4 class="text-green"> Upload a File </h4>
	@include('general-downloads.views.upload-file')


	<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
	<h3> Files </h3>
	@include('general-downloads.views.list')
@stop