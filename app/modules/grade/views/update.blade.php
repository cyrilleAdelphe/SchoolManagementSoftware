@extends('backend.'.$role.'.main')

@section('page-header')
	<h1>Assign Grade</h1>
@stop

@section('content')
<form method="post" action="{{URL::route('grade-update-post')}}">
	<table id="pageList" class="table table-bordered table-striped">
		<thead>
	    <tr>
	      <th>SN</th>
	      <th>From (%)</th>
	      <th>To (%)</th>
	      <th>Grade</th>
	      <th>Grade Point</th>
	    </tr>
	  </thead>
	  <tbody>
	  	
	  	@for ($i=0; $i < NO_OF_GRADES; $i++)
	  		@if (isset($config[$i]))
	  			@define $from = $config[$i]->from
	  			@define $to = $config[$i]->to
	  			@define $grade = $config[$i]->grade
	  			@define $grade_point = isset($config[$i]->grade_point) ? $config[$i]->grade_point : ''
	  		@else
	  			@define $from = $to = $grade = $grade_point = ''
	  		@endif
	  		<tr>
		      <td>{{$i+1}}</td>
		      <td>
		    		<input id="title" class="form-control" name="from[]" type="text" value="{{$from}}"> 
		      </td>
		      <td>
		        <input id="title" class="form-control" name="to[]"type="text" value="{{$to}}"> 
		      </td>
		      <td>
		        <input id="title" class="form-control" name="grade[]" type="text" value="{{$grade}}"> 
		      </td>
		      <td>
		        <input id="title" class="form-control" name="grade_point[]" type="text" value="{{$grade_point}}"> 
		      </td>
		    </tr>
	  	@endfor
		</tbody>
	</table>
	{{Form::token()}}
	<div class="form-group">
    <button class="btn btn-success btn-lg btn-flat" type="submit" @if(!AccessController::checkPermission('grade', 'can_create,can_edit')) disabled @endif>Submit</button>
  </div>
</form>
@stop