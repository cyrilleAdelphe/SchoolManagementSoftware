              <!-- Billing-v1-changed-made-here -->
              <div class="row notes">
                <div class="col-sm-5">
                  <table class="table table-bordered table-striped myData">
                    <div class="mTitle">Quick Summary - Report of all classes</div>
                    <tr>
                      <th>Total Amount</th>
                      <td>{{$data['total_amount_total']}}</td>
                    </tr>
                    
                    @define $total = 0
                    @foreach($data['fee_titles'] as $fee_title => $amount)
                    <tr>
                      <td><strong>{{BillingHelperController::removeQuotesAndUnderScore($fee_title)}}:</strong></td>
                      <td>{{(float) $amount}}</td>
                      @define $total += $amount
                    </tr>
                    @endforeach
                    <tr>
                      <th>Credit Note Total</th>
                      <td>{{$data['credit_note_total']}}</td>
                    </tr>
                    <tr>
                      <th>Credit Note Tax Total</th>
                      <td>{{$data['credit_note_tax_total']}}</td>
                    </tr>
                    <tr>
                      <th>Flat Discounts Total</th>
                      <td>{{$data['flat_discounts_total']}}</td>
                    </tr>
                    <tr>
                      <td class="text-red"><strong>Received Amount Total:</strong></td>
                      <td class="text-red">{{$data['received_amount_total']}}</td>
                    </tr>
                    <tr>
                      <td class="text-red"><strong>Unpaid Amount total:</strong></td>
                      <td class="text-red">{{$data['unpaid_amount_total']}}</td>
                    </tr>
                    
                  </table>
                </div>
              </div>
              <div class= "table-responsive">
              <table  class="table table-bordered table-striped myData">
                <tbody>
                  @foreach($data['data'] as $class_section => $details)
                  <tr>
                    <th>Class</th>
                    <th>Total Amount</th>
                    @foreach($details['fees'] as $fee => $amount)
                    <th>{{BillingHelperController::removeQuotesAndUnderScore($fee)}}</th>
                    @endforeach
                    <th>Credit Note</th>
                    <th>Credit Note Tax</th>
                    <th>Flat Discounts</th>
                    <th>Received Amount</th>
                    <th>Unpaid Amount</th>
                    <th></th>
                  </tr>
                  <tr>
                    <td>{{$class_section}}</td>
                    <input type="hidden" class="class_section" value="{{$class_section}}">
                    <td>{{(float) $data['total_amount'][$class_section]}}</td>
                    
                    @foreach($details['fees'] as $fee => $amount)
                    <td>{{(float) $amount}}</td>
                    @endforeach

                    @define $credit_note_data = isset($data['credit_note'][$class_section]['total']) ? $data['credit_note'][$class_section]['total'] : 0
                    <th>{{$credit_note_data}}</th>

                    @define $credit_note_tax_data = isset($data['credit_note'][$class_section]['tax']) ? $data['credit_note'][$class_section]['tax'] : 0
                    <th>{{ $credit_note_tax_data }}</th>
                    
                    
                    @define $flat_discounts_data = isset($data['flat_discounts'][$class_section]) ? $data['flat_discounts'][$class_section] : 0
                    <th>{{$flat_discounts_data}}</th>
                    
                    <td>{{(float) $data['received_amount'][$class_section]}}</td>
                     
                    
                    <td>{{(float) $data['unpaid_amount'][$class_section]}}</td>
    <td>
                     
    <div class = "form-group">
      
        <input type = "submit" class = "btn btn-flat btn-success submit-enable-disable submit" related-form = "showStudents"  value = "Students">
      
    </div></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              </div>
<!-- Billing-v1-changed-made-here -->