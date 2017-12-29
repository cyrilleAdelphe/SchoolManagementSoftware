@extends('backend.'.$current_user->role.'.main')
@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{ asset('sms/assets/css/lity.min.css')}}">
@stop
@section('page-header')    
  <h1>Students Report</h1>
@stop
@section('content')
<!-- StudentReport-dynamic-header-titles-v1-changes-made-here -->
<a href = "{{ URL::route('student-show-report-config-post') }}" class = "btn btn-danger">Set Report Columns</a>
<!-- StudentReport-dynamic-header-titles-v1-changes-made-here -->

<form method="get" action="{{URL::route('show-report-get')}}">
 <div class="row">

                    <div class="col-sm-2">
                      <div class="form-group">
                        <label>Select Session</label>
                        
                         {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
	        	$selected = 
	          Input::has('academic_session_id') ?
	          Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
                        
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group">
                        <label>From Class</label>
                        <select id="class_id" class="form-control" name="class_1">
                        	
                 	       <option value="all">All</option>
                          @foreach($class_list as $index=>$value)
                          <option value="{{ $index}}">{{$value}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group">
                        <label>From Section</label>
                        <select id="section_id" class="form-control" name="section_1">
                          <option value="all">All</option>
                          
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group">
                        <label>To Class</label>
                        <select id="class_id1" class="form-control" name="class_2">
                          
                          <option value="all">All</option>
                          @foreach($class_list as $index=>$value)
                          <option value="{{ $index}}">{{$value}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <div class="form-group">
                        <label>To Section</label>
                        <select id="section_id1" class="form-control" name="section_2">
                           <option value="all">All</option>
                        </select>
                      </div>
                    </div>
              </div><!-- row ends -->
              <label class="text-red" style="font-size:16px">Please select the criterion</label>
              <div class="row">
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Age from</label>
                    <input type="number" name="age_1" id = "age_1" class="form-control" />
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label>Age to</label>
                    <input type="number" id="age_2" name="age_2" class="form-control" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2">
                      <div class="form-group">
                        <label>Gender</label>
                        <select id="gender_id" class="form-control" name="gender">
                          <option value="all">All</option>
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                        </select>
                      </div>
                </div>
                <div class="col-sm-2">
                      <div class="form-group">
                        <label>House</label>
                        <select id="house_id" class="form-control" name="house">
                          <option value="all">All</option>
                          @foreach($house_list as $index => $value)
                          <option value="{{ $index}}">{{ $value}}</option>
                          @endforeach
                          
                        </select>
                      </div>
                </div>
                <div class="col-sm-2">
                      <div class="form-group">
                        <label>Discount host</label>
                        <select id="discount_id" class="form-control" name = "discount_org_id">
                          <option value="0">Select</option>
                          @foreach($discount_host as $index=>$value)
                          <option value="{{$index}}">{{ $value}}</option>
                          @endforeach
                        </select>
                      </div>
                </div>
                <div class="col-sm-2" id="dis_title_group">
                      <div class="form-group">
                        <label>Discount type</label>
                        <select id="discount_type" class="form-control" name = "discount_type">
                       
                       
                        </select>
                      </div>
                </div>
                <div class="col-sm-2">
                      <div class="form-group">
                        <label>Ethnicity</label>
                        <select id="ethnicity_id" class="form-control" name="ethnicity">
                          <option value="all">All</option>
                          @foreach($ethnicity_list as $index=>$value)
                          <option value="{{$index}}">{{$value}}</option>
                          @endforeach
                        </select>
                      </div>
                </div>                
                <div class="col-sm-2">
                      <div class="form-group">
                        <label>Facility</label>
                        <select id="facility_id" class="form-control" name="facility">
                          <option value="0">Select</option>
                          <option value="transportation">Transportation</option>
                          
                        </select>
                      </div>
                </div>                    
              </div><!-- row ends -->
              <input type="hidden" name= "default_class" id="default_class" value="{{Input::has('class_id')?Input::get('class_id'):''}}" />
       		 <a href ="{{URL::route('show-report-get')}}" id ="report_btn" class="btn btn-lg btn-flat btn-success" >Show report</a>
           {{Form::token()}}
           </form>
     
@stop
@section('custom-js')
<script type="text/javascript" src ="{{ asset('sms/assets/js/lity.min.js')}}"></script>

<script type="text/javascript">
	$(function() {
		$('#dis_title_group').hide(); 
		$(document).on('change', '#class_id', updateSectionList);
		$(document).on('change', '#class_id1', updateSectionList1);
		$(document).on('change', '#discount_id', updateDiscountList);
    $("#report_btn" ).on( "click", function(e)
      {
        e.preventDefault();

        var academic_session_id = $('#academic_session_id').val();
        var class_1 = $('#class_id').val();
        var section_1 = $('#section_id').val();
        var class_2 = $('#class_id1').val();
        var section_2 = $('#section_id1').val();
        var age_1 = $('#age_1').val();
        var age_2 = $('#age_2').val();
        var gender = $('#gender_id').val();
        var house = $('#house_id').val();
        var discount_org_id = $('#discount_id').val();
        var discount_type = $('#discount_type').val();
        if(discount_type)
        {

        }
        else
        {
          discount_type = 0;
        }
        var ethnicity = $('#ethnicity_id').val();
        var facility = $('#facility_id').val();


        var url = $(this).attr('href');
        url = url + "?academic_session_id=" + academic_session_id + "&class_1=" +class_1 +"&section_1=" +section_1 +"&class_2=" + class_2+ "&section_2=" +section_2 +"&age_1="+age_1 +"&age_2=" +age_2 + "&gender=" +gender+ "&house=" +house +"&discount_org_id=" +discount_org_id+ "&discount_type=" + discount_type+ "&ethnicity="+ ethnicity+"&facility="+facility;
        var lightbox = lity(url);
        //AjaXSendFormParameters();
      });
    
	});
			
	function updateSectionList()
  {
  	var class_id = $('#class_id').val();
  	 	
  	
    $('#section_id').html('<option value="0">Loading...</option>');
    $.ajax({
                      "url": "{{URL::route('class-section-report-get')}}",
                      "data": {"class_id":class_id
                  				},
                      "method": "GET"
                      } ).done(function(data) {

                  $('#section_id').html(data);
           
                });
  }

  function updateSectionList1()
  {
  	var class_id = $('#class_id1').val();
  	  	
  	
    $('#section_id1').html('<option value="0">Loading...</option>');
    $.ajax({
                      "url": "{{URL::route('class-section-report-get')}}",
                      "data": {"class_id":class_id
                  				},
                      "method": "GET"
                      } ).done(function(data) {

                  $('#section_id1').html(data);
            
                });
  }

  function updateDiscountList()
  {	
  	var discount_org_id = $('#discount_id').val();
   	       	$('#dis_title_group').show(); 
              $('#discount_type').html('<option value="0">Loading...</option>');
              $.ajax({
              	"url" : "{{ URL::route('discount-type-report-get')}}",
              	"data": {"discount_org_id": discount_org_id},
              	"method" :  "GET"
              }).done(function(data) {

              	$('#discount_type').html(data);
              

              });

  }
	
  function AjaXSendFormParameters()
  {
      
    var academic_session_id = $('#academic_session_id').val();
    console.log(academic_session_id);
    var class_1 = $('#class_id').val();
    var section_1 = $('#section_id').val();
    var class_2 = $('#class_id1').val();
    var section_2 = $('#section_id1').val();
    var age_1 = $('#age_1').val();
    var age_2 = $('#age_2').val();
    var gender = $('#gender_id').val();
    var house = $('#house_id').val();
    var discount_org_id = $('#discount_id').val();
    var discount_type = $('#discount_type').val();
    var ethnicity = $('#ethnicity_id').val();
    var facility = $('#facility_id').val();
      
      $.ajax({
        type : 'get',
        url  : '{{URL::route('show-report-get')}}',
        data : { "academic_session_id": academic_session_id,
          "class_1" : class_1,
          "section_1": section_1,
          "class_2": class_2,
          "section_2": section_2,
          "age_1": age_1,
          "age_2": age_2,
          "gender": gender,
          "house": house,
          "discount_org_id": discount_org_id,
          "discount_type": discount_type,
          "ethnicity": ethnicity,
          "facility": facility},
        success:function(data){

         

          $('#pageList').html(data);
        }
      });

     
  }

</script>

@stop
@stop
