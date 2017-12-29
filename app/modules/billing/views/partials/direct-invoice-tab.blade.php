@extends('billing.views.form-tabs')

@section('page-header')
  <h1>List View</h1>
@stop

@section('custom-css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<link href="{{asset('sms/assets/css/lity.min.css')}}" rel="stylesheet" type="text/css" />

<link href="{{asset('sms/assets/css/nepali.datepicker.v2.2.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('tab-content')
            
            <form method="GET" action="{{URL::route('billing-fee-print-direct-invoice')}}">
              <div class="row">
                
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Session</label>
                    @define $sessions = AcademicSession::where('is_active', 'yes')->where('is_current', 'yes')->select('session_name', 'id', 'is_current')->get();

                  <select class = "academic_session_id form-control" id = "academic_session_id" name = "academic_session_id">
                  @foreach($sessions as $s)
                    <option value = "{{$s->id}}" @if($s->is_current == 'yes') selected @endif>{{$s->session_name}}</option>
                  @endforeach
                  </select>
                  </div>
                </div>                
                <div class="col-sm-2 auto-off-block">
                  <div class="form-group">
                    <label>Class</label>
                    <select class="form-control class_id" name = "class_id" id="class_id">
                    <option value="0">-- Select Session First --</option>
                  </select>
                  </div>
                </div>
                <div class="col-sm-2 auto-off-block">
                  <div class="form-group">
                    <label>Section</label>
                    <select id="section_id"  name = "section_id" class="form-control academic_session_id" >
                      <option value="0">-- Select Class First --</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Date</label>
                    <input type="text" id="nepaliDate" name = "nepDate" class="nepali-calendar hidNep form-control" value=""/>
                  </div>
                </div>
                <input type = "hidden" name = "month" id = "month" class = "month">

                <div class="col-sm-2">
                  <div class="form-group">
                    
                    @define $date = date('Y-m-d')
                    <input type="hidden" id="englishDate" class="form-control" name="issued_date" value = "{{$date}}"/>
                  </div>
                </div>

                

                 <div class="col-sm-1">
                  <div class="form-group">
                  <label style="color: #fff">Show</label>
                   <button type="submit" class="btn btn-success btn-flat" id = "show-statement-button">Show</button>
                  </div>
                </div>

 
              </div> <!-- row ends -->
              </form>
              
              <div id = "ajax-content">
              
              </div>

              <form method="post">  
               
              <input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
              <input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
              
              </form>  
@stop

@section('custom-js')

<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script type="text/javascript" src="{{asset('sms/assets/js/lity.min.js')}}"></script>
<script src="{{asset('sms/assets/js/nepali.datepicker.v2.2.min.js')}}" type="text/javascript"></script>
    <script>
    $(document).ready(function()
      {
        $('.hidNep').val(AD2BS($('#englishDate').val()));
        
      $('#nepaliDate').nepaliDatePicker({
            ndpEnglishInput: 'englishDate'
            
        });
    });
      updateClassList();
      updateSectionList();
      
      $(document).on('change', '#academic_session_id', function(e)
      {
        
        updateClassList();
        updateSectionList();
        
        
        
      });

      $(document).on('change', '#class_id', function(e)
      {
        
        
        updateSectionList();
        
        
      });



      

    

     


      function updateClassList()
      {
        var session_id = $('#academic_session_id');
        var class_id = $('#class_id');

        class_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id.val(), 'extra' : ''}
        }).done(function(data)
        {
          class_id.html(data);
        });
      }

      function updateSectionList()
      {
        var class_id = $('#class_id');
        var section_id = $('#section_id');

        section_id.html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id.val(), 'extra' : ''}
        }).done(function(data)
        {
          section_id.html(data);
        });
      }

      $(document).on('click', '.auto-off', function(e)
      {
        console.log('off');
        e.preventDefault();
        $('.auto-off-block').css('display', 'none');
        $('.auto-on-block').css('display', 'block');
        $(this).removeClass('auto-off');
        $(this).addClass('auto-on');
       

      });

      $(document).on('click', '.auto-on', function(e)
      {
        console.log('on');
        e.preventDefault();
        $('.auto-off-block').css('display', 'block');
        $('.auto-on-block').css('display', 'none');
        $(this).removeClass('auto-on');
        $(this).addClass('auto-off');
       

      });

     
    </script>
@stop