@define $status = 0

<div class="info-bar">
                Statement of Student: {{StudentRegistration::where('id', $student_id)->pluck('student_name')}} | Session {{AcademicSession::where('id', $session_id)->pluck('session_name')}} |  Class: {{Classes::where('id', $class_id)->pluck('class_name')}} {{Section::where('id', $section_id)->pluck('section_code')}} | Date from: {{$date_range}}
              </div>
              <table  class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>SN</th>
                      <th>Transaction date</th>
                      <th>Transaction no.</th>
                      <th>Transaction type</th>
                      <th>Invoice amount</th>
                      <th>Amount received</th>
                      <th>Balance</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="6" style="text-align: right"><strong>Opening balance as on {{$data['dates'][0]}}</strong></td>
                      <td><strong>{{$data['opening_balance']}}</strong></td>
                    </tr>
                    @define $i = 0
                    @foreach($data['data'] as $d)
                    @define $status = 1
                    <tr>
                      <td>{{++$i}}</td>
                      <td>{{$d->transaction_date}}</td>
                      <td><a href = "{{URL::route('show-invoice-from-transaction-number', $d->transaction_no)}}??apiToken={{ preg_replace( "/\r|\n/", "", Input::get('apiToken', base64_encode('0:0')) ) }}" data-lity >{{$d->transaction_no}}</a></td>
                      <td>{{$d->transaction_type}}</td>
                      @if(in_array($d->transaction_type, SsmConstants::$const_billing_types['credit']))
                      <td>{{$d->transaction_amount}}</td>
                      <td></td>
                      @else
                      <td></td>
                      <td>{{$d->transaction_amount}}</td>
                      @endif
                      <td>{{$d->balance_amount}}</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td colspan="6" style="text-align: right"><strong>Closing balance as on {{$data['dates'][1]}}</strong></td>
                      <td><strong>@if($status) {{end($data['data'])->balance_amount}} @else {{$data['opening_balance']}} @endif</strong></td>

                    </tr>
                  </tbody>
                </table>