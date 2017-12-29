@extends('backend.'.$role.'.main')

@section('page-header')    
  <h1>Contact Manager</h1>
@stop

@section('content')
             

    {{$actionButtons}}
    
        <div class = 'form-group'>
			<label for = 'sender_email'  class = 'control-label'>Sender Email :</label>
			<p>{{$data->sender_email}}</p>
		</div>

		<div class = 'form-group'>
			<label for = 'sender_location'  class = 'control-label'>Sender Location :</label>
			<p>{{$data->sender_location}}</p>
		</div>

		
		<div class = "form-group">
			<label for = "subject">Subject:</label>
			<p>{{$data->subject}}</p>
		</div>

		<div class = "form-group">
			<label for = "query">Query:</label>
			<p>{{$data->query}}</p>
		</div>                         
</div>
     
@stop

