<div class="row">
  <div class="col-sm-3">
    <div class="form-group">
      <label>Fee type</label>      
      <select class = "form-control fee_type" name = "fee_type[]">
      @foreach($fees as $fee)      
        <option value = "{{$fee->fee_category}}">{{$fee->fee_category}}</option>
      @endforeach
      </select>
      <input type = "hidden" name = "tax_applicable[]" class = "tax_applicable" value = "{{$fee[0]->tax_applicable}}">
    </div>
  </div>

  <div class="col-sm-5">
    <div class="form-group">
      <label>Description</label>
      <input type = "text" class = "form-control note" name="note[]" >
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label>Amount</label>
      <input type = "text" class = "form-control fee_amount" name = "fee_amount[]" >
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label style="color: #fff; display: block">Add more</label>
      <a href="#" class="btn btn-primary btn-flat add_field_button">Add more </a>
    </div>
  </div>

</div>