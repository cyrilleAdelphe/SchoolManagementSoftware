              <div class="row notes">
                <div class="col-sm-4">
                  <table class="table table-bordered table-striped myData">
                    <div class="mTitle">Quick Summary</div>
                    <tr>
                      <td><strong>No. of Invoice:</strong></td>
                      <td>{{$data['aggregate']['count']}}</td>
                    </tr>
                    <tr>
                      <td><strong>Tax extempt amount:</strong></td>
                      <td>{{$data['aggregate']['untaxable_amount']}}</td>
                    </tr>
                    <tr>
                      <td><strong>Taxable amount:</strong></td>
                      <td>{{$data['aggregate']['taxable_amount']}}</td>
                    </tr>
                    <tr>
                      <td><strong>Tax:</strong></td>
                      <td>{{$data['aggregate']['tax']}}</td>
                    </tr>
                    <tr>
                      <td><strong>Total:</strong></td>
                      <td>{{$data['aggregate']['total']}}</td>
                    </tr>
                  </table>
                </div>
              </div>
              <table class="table table-bordered table-striped myData">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>Invoice date</th>
                    <th>Invoice no.</th>
                    <th>Student's name</th>
                    <th>Tax extempt amount</th>
                    <th>Taxable amount</th>
                    <th>Tax</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  @define $i = 0
                  @foreach($data['data'] as $d) 
                  <tr>
                    <td>{{++$i}}</td>
                    <td>{{$d->issued_date}}</td>
                    <td><a href="{{ URL::route('show-invoice-from-invoice-number', $d->invoice_number) }}?financial_year={{$d->financial_year}}">{{$d->invoice_number}}</a></td>
                    <td>{{$d->invoice_details->personal_details->name}}</td>
                    <td>{{$d->invoice_details->summary->untaxable_amount}}</td>
                    <td>{{$d->invoice_details->summary->taxable_amount}}</td>
                    <td>{{$d->invoice_details->summary->tax}}</td>
                    <td>{{$d->invoice_details->summary->total}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
