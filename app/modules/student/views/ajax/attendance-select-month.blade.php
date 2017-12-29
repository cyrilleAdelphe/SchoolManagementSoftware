<div class="row">
  <div class="col-sm-3">
    <div class="form-group">
      <label>Month</label>
      @if(CALENDAR=='BS')
        @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):HelperController::getCurrentNepaliMonth())
        @define $months = array('Baisakh', 'Jestha', 'Ashad', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra');
      @else
        @define $default_month = Input::old('month')?Input::old('month'):(Input::has('month')?Input::get('month'):date('m'))
        @define $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'Novemeber', 'December')
      @endif
      
      <select class="form-control" name="month" id="month">
      @foreach($months as $key=>$month_name)
      	@define $month_id = $key + 1
      	<option value="{{$month_id}}" @if($default_month==$month_id) selected @endif>{{$month_name}}</option>
      @endforeach
      </select>
    </div>
  </div>
</div>

<div id="attendanceMonth">
</div>

<script type="text/javascript">
  function updateAttendance() {
    $('#attendanceMonth').html('<div class="dloading"><img src="{{ asset('sms/assets/img/loading.gif') }}"><br/>loading...</div>');
    $.get(
      '{{ URL::route('student-ajax-attendance-get-month') }}',
      {
        'student_id': '{{ $student_id }}',
        'month': $('#month').val()
      },
      function(data, status) {
        if (status) {
          $('#attendanceMonth').html(data);
        }
      }
    );
  }
  
  updateAttendance();
  $('#month').click(updateAttendance);
</script>