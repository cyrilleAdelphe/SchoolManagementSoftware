@extends('backend.'.$role.'.main')
@section('content')

@if($data)
	<div class = 'content'>
		<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
				
				<div class = 'form-group @if($errors->has("remarks_number")) {{"has-error"}} @endif'>
					<label>Number</label>
					<input type = 'text' name = 'remarks_number' value = '{{$data->remarks_number}}' class = "required form-control"><span class = 'help-block'>@if($errors->has('remarks_number')) {{$errors->first('remarks_number')}} @endif</span>
				</div>

				<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
					<label>Remarks</label>
					<input type = 'text' name = 'remarks' value = '{{$data->remarks}}' class = "required form-control"><span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
				</div>

				

			<div class="form-group">
				{{Form::token()}}
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
			</div>
		<input type = "hidden" name = "id" value = "{{$data->id}}">
		</form>
	</div>
@else
	<h1>No Record Found</h1>
@endif

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "javascript/text"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
@stop


