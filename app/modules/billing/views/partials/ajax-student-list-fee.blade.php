@define $transportation_fee_id = 0
@define $status = count($save_data) ? true : false
@define $view['config'] = json_decode(File::get(app_path().'/modules/billing/config/config.json'), true)

<div class="row">
  <div class="col-sm-12" style="margin: 10px 0">
    
  </div>
</div>
<div class="info-bar">
  Data of Session {{HelperController::pluckFieldFromId('AcademicSession', 'session_name', $view['academic_session_id'])}} |  Class: {{HelperController::pluckFieldFromId('Classes', 'class_name', $view['class_id'])}} {{HelperController::pluckFieldFromId('Section', 'section_code', $view['section_id'])}} | Student: All | Month: {{$view['month']}}
</div>

<div class="dataTable">
<div class = "table-responsive">
<table  class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Roll no</th>
        <th>Name</th>
        @foreach($view['fees'] as $fee)
          <th>{{$fee['fee_category']}}</th>
          @if($fee['fee_category'] == 'Transportation Fee')
            @define $transportation_fee_id = $fee['fee_id']
          @endif
        @endforeach
        <th>Late Fee</th>
        @foreach($view['discounts']['discount_fees'] as $organization_id => $organization)
          @foreach($organization['discounts'] as $discount_id => $discount)
            @foreach($discount['fees'] as $fee_id => $d)
              <th>Discount on {{$d}} by {{$organization['organization_name']}}</th>
            @endforeach
          @endforeach
        @endforeach
        <th>Taxable Amount</th>
        <th>Tax exempt Amount</th>
        <th>Total without tax</th>
        <th>Tax</th>
        <th>Total</th>
        <th>Notes</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($view['students'] as $student_id => $s)
      <tr>
        <input type = "hidden" name = "student_id[{{$student_id}}]" value = "{{$student_id}}">
        <td>
          <span>{{$s['roll']}}</span>
          <span><input type = "hidden" name = "roll_number[{{$student_id}}]" value = "{{$s['roll']}}"></span>
        </td>
        <td>
          @if(strlen($s['name']) > 14)<a href="" data-Toggle="tooltip" title="{{$s['name']}} {{$s['last_name']}} ">{{substr($s['name'], 0,14)}}...</a>@else 

{{$s['name']}} {{$s['last_name']}}  @endif
         <!-- Billing-v1-changed-made-here -->
         <input type = "hidden" name = "name[{{$student_id}}]" value = "{{$s['name'].' '.$s['last_name']}}">
         <!-- Billing-v1-changed-made-here -->
        </td>
        @define $taxable_sum = 0
        @define $untaxable_sum = 0
          @foreach($view['fees'] as $fee)
            
            <td>
            @if($fee['tax_applicable'] == 'yes')

                @if($fee['fee_category'] == 'Transportation Fee')
                  <span class = "editable_data">@if($status) {{$save_data['fees'][$student_id][$fee['fee_category']]}} <?php $fee['amount'] = $save_data['fees'][$student_id][$fee['fee_category']]; ?> @else <?php $fee['fee_amount'] = isset($view['transportation_fees'][$student_id]) ? $view['transportation_fees'][$student_id] : 0; ?> {{$fee['fee_amount']}} @endif<?php $taxable_sum+=$fee['fee_amount'] ?></span>

                  <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}][{{$fee['fee_category']}}]" class = "billing_editable taxable" value = @if($status) "{{$save_data['fees'][$student_id][$fee['fee_category']]}}" @else <?php $fee['fee_amount'] = isset($view['transportation_fees'][$student_id]) ? $view['transportation_fees'][$student_id] : 0; ?> "{{$fee['fee_amount']}}" @endif></span>
                @else

                  <span class = "editable_data">@if($status) {{$save_data['fees'][$student_id][$fee['fee_category']]}} <?php $fee['amount'] = $save_data['fees'][$student_id][$fee['fee_category']]; ?> @elseif(isset($view['related_extra_fees_students'][$student_id][$fee['fee_id']])) {{$view['related_extra_fees_students'][$student_id][$fee['fee_id']]}}<?php $fee['fee_amount'] = $view['related_extra_fees_students'][$student_id][$fee['fee_id']]; ?> @else {{$fee['fee_amount']}} @endif<?php $taxable_sum+=$fee['fee_amount'] ?></span>

                  <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}][{{$fee['fee_category']}}]" class = "billing_editable taxable" value = @if($status) "{{$save_data['fees'][$student_id][$fee['fee_category']]}}" @elseif(isset($view['related_extra_fees_students'][$student_id][$fee['fee_id']])) {{$view['related_extra_fees_students'][$student_id][$fee['fee_id']]}}<?php $fee['fee_amount'] = $view['related_extra_fees_students'][$student_id][$fee['fee_id']]; ?> @else "{{$fee['fee_amount']}}" @endif></span>
                @endif
              
              
            @else
                
               @if($fee['fee_category'] == 'Transportation Fee')
                  <span class = "editable_data">@if($status) {{$save_data['fees'][$student_id][$fee['fee_category']]}} <?php $fee['amount'] = $save_data['fees'][$student_id][$fee['fee_category']]; ?> @else <?php $fee['fee_amount'] = isset($view['transportation_fees'][$student_id]) ? $view['transportation_fees'][$student_id] : 0; ?> {{$fee['fee_amount']}} @endif<?php $untaxable_sum+=$fee['fee_amount'] ?></span>
                  <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}][{{$fee['fee_category']}}]" class = "billing_editable" value = @if($status) "{{$save_data['fees'][$student_id][$fee['fee_category']]}}" @else <?php $fee['fee_amount'] = isset($view['transportation_fees'][$student_id]) ? $view['transportation_fees'][$student_id] : 0; ?> "{{$fee['fee_amount']}}" @endif></span>
                
                @else
                  <span class = "editable_data">@if($status) {{$save_data['fees'][$student_id][$fee['fee_category']]}} @elseif(isset($view['related_extra_fees_students'][$student_id][$fee['fee_id']])) {{$view['related_extra_fees_students'][$student_id][$fee['fee_id']]}} <?php $fee['fee_amount'] = $view['related_extra_fees_students'][$student_id][$fee['fee_id']]; ?> @else {{$fee['fee_amount']}} @endif<?php $untaxable_sum+=$fee['fee_amount'] ?></span>

                  <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}][{{$fee['fee_category']}}]" class = "billing_editable" value = @if($status) "{{$save_data['fees'][$student_id][$fee['fee_category']]}}" @elseif(isset($view['related_extra_fees_students'][$student_id][$fee['fee_id']])) {{$view['related_extra_fees_students'][$student_id][$fee['fee_id']]}}<?php $fee['fee_amount'] = $view['related_extra_fees_students'][$student_id][$fee['fee_id']]; ?> @else  "{{$fee['fee_amount']}}" @endif></span>
                @endif

             
            @endif
            <input type = "hidden" name = "fee_type[{{$fee['fee_category']}}]" value = "{{$fee['tax_applicable']}}">
            </td>
          @endforeach
          
          <td>
            @if($status)
              ({{$save_data['fees'][$student_id]['late_fees_amount']}})
            @else
              @if(isset($view['late_fees'][$student_id])) ({{$view['late_fees'][$student_id]}}) @else {{'(NA)'}} @endif
            @endif

            @if($view['config']['late_fee_tax_applicable'] == 'yes')
              <span class = "editable_data">@if($status)  <?php $fee['amount'] = $save_data['fees'][$student_id]['late_fees']; ?> @else <?php $fee['fee_amount'] = isset($view['late_fees'][$student_id]) ? $view['config']['late_fee_amount'] : 0; ?>  {{$fee['fee_amount']}} @endif<?php $taxable_sum+=$fee['fee_amount'] ?>
              </span>

              <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}]['late_fees']" class = "billing_editable taxable" value = "{{$fee['fee_amount']}}">
              </span>

            @else
              <span class = "editable_data">@if($status)  <?php $fee['amount'] = $save_data['fees'][$student_id]['late_fees']; ?> @else <?php $fee['fee_amount'] = isset($view['late_fees'][$student_id]) ? $view['config']['late_fee_amount'] : 0; ?>  {{$fee['fee_amount']}} @endif<?php $untaxable_sum+=$fee['fee_amount'] ?>
              </span>

              <span class = "editable_hidden_data"><input type = "hidden" name = "fees[{{$student_id}}]['late_fees']" class = "billing_editable untaxable" value = "{{$fee['fee_amount']}}">
              </span>
            @endif

            <input type = "hidden" name = "fees[$student_id]['late_fees_amount']" value = "@if(isset($view['late_fees'][$student_id])) {{$view['late_fees'][$student_id]}} @else NA @endif">

            <input type = "hidden" name = "fee_type['late_fees']" value = "{{$view['config']['late_fee_tax_applicable']}}">

          </td>

          @foreach($view['discounts']['discount_fees'] as $organization_id => $organization)
            @foreach($organization['discounts'] as $discount_id => $discount)
              
              @foreach($discount['fees'] as $fee_id => $d)
                <td>
                @if(isset($view['discounts']['discount_students'][$student_id][$organization_id][$discount_id][$fee_id]))
                  @if($fee_id == $transportation_fee_id)
                    
                    @define $amount = isset($view['transportation_fees'][$student_id]) ? $view['transportation_fees'][$student_id] : 0
                    
                    @define $discount_amount = BillingHelperController::calculateDiscount($amount, $view['discounts']['discount_students'][$student_id][$organization_id][$discount_id][$fee_id])
                  
                  @elseif(isset($view['related_extra_fees_students'][$student_id][$fee_id]))
                    @define $amount = $view['related_extra_fees_students'][$student_id][$fee_id]

                    @define $discount_amount = BillingHelperController::calculateDiscount($amount, $view['discounts']['discount_students'][$student_id][$organization_id][$discount_id][$fee_id])

                  @else
                    @define $discount_amount = BillingHelperController::calculateDiscount($view['fees_amount'][$fee_id], $view['discounts']['discount_students'][$student_id][$organization_id][$discount_id][$fee_id])

                  @endif
                  @if($discount[$fee_id]['tax_applicable'] == 'yes')
                    @define $taxable_sum -= $discount_amount
                  @else
                    @define $untaxable_sum -= $discount_amount
                  @endif
                  <span class = "editable_data">@if($status) {{$save_data['discount'][$organization['organization_name']][$student_id][$discount['discount_name']][$d]}} @else {{$discount_amount}} @endif</span>
                  <span class = "editable_hidden_data">
                    <input class = "taxable-minus-{{$discount[$fee_id]['tax_applicable']}} billing_editable" type = "hidden" name = "discount[{{$organization['organization_name']}}][{{$student_id}}][{{$discount['discount_name']}}][{{$d}}]" value = @if($status) "{{$save_data['discount'][$organization['organization_name']][$student_id][$discount['discount_name']][$d]}}" @else "{{$discount_amount}}" @endif>
                    <input type = "hidden" name = "invoice[{{$organization_id}}][{{$organization['organization_name']}}]" value = "{{$organization['generate_invoice']}}">
                    
                  </span>
                @else
                  <span>NA</span>
                @endif
                </td>
              @endforeach
            @endforeach
          @endforeach
          
        <td>
          <span class = "taxable-sum">@if($status) {{$save_data['taxable'][$student_id]}} @else {{$taxable_sum}} @endif</span>
          <span class = "editable_hidden_data"><input class = "taxable-sum-data" type = "hidden" name = "taxable[{{$student_id}}]" value = @if($status) "{{$save_data['taxable'][$student_id]}}" @else "{{$taxable_sum}}" @endif></span>
        </td>
        <td>
          <span class = "untaxable-sum">@if($status) {{$save_data['untaxable'][$student_id]}} @else {{$untaxable_sum}} @endif</span>
          <span class = "editable_hidden_data"><input class = "untaxable-sum-data" type = "hidden" name = "untaxable[{{$student_id}}]" value = @if($status) "{{$save_data['untaxable'][$student_id]}}" @else "{{$untaxable_sum}}" @endif></span>
        </td>
        <td>
          <span class= "sum-without-tax">@if($status) {{$save_data['sum_without_tax'][$student_id]}} @else {{$taxable_sum + $untaxable_sum}} @endif</span>
          <span class = "editable_hidden_data"><input class = "sum-without-tax-data" type = "hidden" name = "sum_without_tax[{{$student_id}}]" value = @if($status) "{{$save_data['sum_without_tax'][$student_id]}}" @else "{{$taxable_sum + $untaxable_sum}}" @endif></span>
        </td>
        <td>
          <span class = "tax">@if($status) {{$save_data['tax'][$student_id]}} @else 
                                        @define $tax = BillingHelperController::calculateTax($taxable_sum)
                                        {{$tax}}
                                        @define $taxable_sum += $tax
                              @endif
          </span>
          <span class = "editable_hidden_data"><input type = "hidden" name = "tax[{{$student_id}}]" value = @if($status) "{{$save_data['tax'][$student_id]}}" @else "{{$tax}}" @endif class = "billing_editable tax-data"></span>
        </td>
        <td>
          <span class = "sum">@if($status) {{$save_data['sum'][$student_id]}} @else {{$taxable_sum + $untaxable_sum}} @endif</span>
          <span class = "editable_hidden_data"><input type = "hidden" name = "sum[{{$student_id}}]" value = @if($status) {{$save_data['sum'][$student_id]}} @else "{{$taxable_sum + $untaxable_sum}}" @endif class = "sum-data"></span>
        </td>

        <td> <a href="#" data-target="#view{{$student_id}}" data-toggle="modal" class="btn btn-default btn-flat btn-xs">View</a>
            <div id="view{{$student_id}}" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Note for {{$s['name']}}</h4>
                    <small class="text-light-blue"></small>
                  </div>
                  <div class="modal-body">
                    <textarea name = "note[{{$student_id}}]"></textarea>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div> 
        </td>
        <td>
          <a href="#" data-toggle="tooltip" title="Edit" class="btn btn-info btn-flat btn-sm billing_editable_row" type="button">
            <i class="fa fa-fw fa-edit"></i>
          </a>
        </td>
      </tr>
      @endforeach
      
    </tbody>
  </table>
</div>
  <input type = "hidden" name = "json_data" value = '{{json_encode($view)}}'>
</div><!-- data table ends -->


