@extends('backend.'.$role.'.main')

@section('custom-css')
<link href="{{asset('sms/assets/css/billing-custom.css')}}" rel="stylesheet" type="text/css" />
@stop


@section('page-header')
  <h1>Late Fee Setting</h1>
@stop

@section('content')
  <div class="row">
  <div class="col-sm-7">
 
    <form action = "{{URL::route('billing-late-fee-post')}}" method = "post">       
        <div class="form-group">
          <label for="fee_category">Start Month</label>
          <input id="fee_category" class="form-control" type="text" name = "start_month" value="{{$access['start_month']}}" disabled>
        </div>  
        <div class="form-group">
          <label for="fee_category">Start Day</label>
          <input id="fee_category" class="form-control" type="text" name = "start_day" value="{{$access['start_day']}}" disabled>
        </div>  
        <div class="form-group">
          <label for="fee_category">Late Fee Days</label>
          <input id="fee_category" class="form-control" type="text" name = "late_fee_days" value="{{$access['late_fee_days']}}">
        </div>  
         <div class="form-group">
          <label for="fee_category">Late Fee Amount</label> 
          <input id="fee_category" class="form-control" type="text" name = "late_fee_amount" value="{{$access['late_fee_amount']}}">
        </div>
         <div class="form-group">
          <label>
            Tax Applicable
          </label><br/>
          <label>
            <input type="radio" name="late_fee_tax_applicable" class="minimal"  value = "yes" checked/> Yes
          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="radio" name="late_fee_tax_applicable" value = "no" class="minimal"/> No
          </label>
        </div>
        <button class="btn btn-flat btn-success btn-lg">Save </button>         
    {{Form::token()}}
   
    </form>
@stop


@section('custom-js')

@stop