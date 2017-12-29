@extends('fee-manager.views.tabs')

@section('tab-content')
<form method="post" action="{{URL::route('fee-manager-tax-config-post')}}">
	<div class="form-group @if($errors->has("tax_percent")) {{"has-error"}} @endif">
		<label> Tax Percent </label>
		<input name="tax_percent" value="{{ $config['tax_percent'] }}" />
		<span class = "form-error">
      @if($errors->has('tax_percent')) 
        {{ $errors->first('tax_percent') }} 
      @endif
    </span>
	</div>

	<div class="form-group">
	  <label for="tax_on" class="control-label">Tax On: </label><br/>
	  @define $taxes = array('monthly', 'examination', 'transportation', 'hostel')
	  @foreach($taxes as $tax)
	  <label>
	    <input type="hidden" name="on_{{ $tax }}" value="no">
	    <input type="checkbox" name="on_{{ $tax }}" value="yes" class="flat-red" {{ $config['on_'.$tax] == 'yes' ? 'checked' : '' }}/>
	    On {{ ucfirst($tax) }}
	  </label>&nbsp;&nbsp;&nbsp
	  @endforeach
	</div>
	{{Form::token()}}
  <div class="form-group">
    <button class="btn btn-primary" type="submit">Submit</button>
  </div>
@stop