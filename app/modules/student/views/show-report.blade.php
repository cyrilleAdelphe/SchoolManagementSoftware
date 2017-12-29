@extends('backend.submain')
@section('content')
<div class="content"> 

      <button class="btn btn-flat btn-default no-print" onclick="javascript:window.print()">Print This report</button>
      <form action="{{ URL::route('generate-student-excel') }}" method="POST" id = "excel-form">
      <div id = "append-data">
      </div>

      <input type="hidden" name="session_id" value="{{ $input_data['academic_session_id']}}">
      <a href = "#" class="btn btn-flat btn-default no-print" id = "download-excel" value="Download Excel" >Download Excel</a>
      <div class="topReportBox">
        <div class="text-red" style="font-size:14px; margin-bottom:10px; font-weight: bold">Report of :</div>
        <strong>From</strong> 
        Class:  @if($input_data['class_1'] == "all") All 
          @define $class_1 = 'All'
        @else 
          {{ DB::table('classess')->where('id',$input_data['class_1'])->pluck('class_name')}} 
          @define $class_1 = DB::table('classess')->where('id',$input_data['class_1'])->pluck('class_name')
        @endif |
        <input type="hidden" name="class_1" value="{{ $class_1 }}">

        Section: @if($input_data['section_1'] == "all") All
          @define $section_1 = 'All'
        @else
          {{DB::table('sections')->where('id',$input_data['section_1'])->pluck('section_code')}}
          @define $section_1 = DB::table('sections')->where('id',$input_data['section_1'])->pluck('section_code')

        @endif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
        <input type="hidden" name="section_1" value="{{ $section_1 }}">
 
        <strong>To</strong> @if($input_data['class_2'] == "all") All
           @define $class_2 = 'All'
        @else
        {{ DB::table('classess')->where('id',$input_data['class_2'])->pluck('class_code')}}  
          @define $class_2 = DB::table('classess')->where('id',$input_data['class_2'])->pluck('class_code')
        @endif | 

        <input type="hidden" name="class_2" value="{{ $class_2}}">

        Section: @if($input_data['section_2'] == "all") All 
          @define $section_2 = 'All'
        @else
        {{ DB::table('sections')->where('id',$input_data['section_2'])->pluck('section_code')}}
          @define $section_2 = DB::table('sections')->where('id',$input_data['section_2'])->pluck('section_code')
         @endif
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

        <input type="hidden" name="section_2" value="{{ $section_2 }}">

         <strong>Age :</strong> 

         {{ $input_data['age_1']}} - {{ $input_data['age_2']}} 

         <input type="hidden" name="age_1" value="{{ $input_data['age_1']}}">

         <input type="hidden" name="age_2" value="{{ $input_data['age_2']}}">
        

        <div class="text-green" style="font-size:14px; margin:10px 0; font-weight: bold">Criterion Details :</div>
        <strong>Gender:  </strong>

        <input type="hidden" name="gender" value="{{ $input_data['gender']}}">
         {{ $input_data['gender']}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

         @if( $input_data['discount_org_id'] != 0 ) 

      <strong>Discount host:</strong> {{ DB::table('billing_discount_organization')->where('id',$input_data['discount_org_id'])->pluck('organization_name')}} 

           @define $discount_org_id =  DB::table('billing_discount_organization')->where('id',$input_data['discount_org_id'])->pluck('organization_name')

          <input type="hidden" name="discount_org_id" value="{{ $discount_org_id }}">

          @else
         <input type="hidden" name="discount_org_id" value="None">

         @endif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

         

         <strong>House: </strong> 
         @if($input_data['house']== "all") All 
           @define $house = 'All'
         @else
           {{ DB::table('houses')->where('id', $input_data['house'])->pluck('house_name')}}
           @define $house = DB::table('houses')->where('id', $input_data['house'])->pluck('house_name')
         @endif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

         <input type="hidden" name="house" value="{{ $house }}">


         <input type="hidden" name="discount_type" value="{{ $input_data['discount_type']}}">
         

         <strong>Ethnicity:</strong> 
          @if($input_data['ethnicity'] == "all") All 
            @define $ethnicity = 'All'
          @else
            {{ DB::table('ethnicity')->where('id',$input_data['ethnicity'])->pluck('ethnicity_name') }}
            @define $ethnicity = DB::table('ethnicity')->where('id',$input_data['ethnicity'])->pluck('ethnicity_name') 
          @endif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 

          <input type="hidden" name="ethnicity" value="{{ $ethnicity }}">
          
          @if($input_data['facility'] == "transportation") <strong>Facility:</strong>  Transportation 
            <input type="hidden" name="facility" value="{{ $input_data['facility']}}">
          @else
           <input type="hidden" name="facility" value="None">
          @endif

          



      </div>
      <div class="row">
        <div class="col-sm-12">
          <table  class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>SN <input type="hidden" name="header[]" value="SN"></th>
                @foreach($columns as $c)
                <th>{{$c['alias']}} <input type="hidden" name="header[]" value="{{$c['alias']}}">  </th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              <tbody>
              @define $i = 1
              @foreach($student_details as $d)
                <tr>
                  <td>{{$i}} <input type="hidden" name="row[{{$i}}][]" value="{{$i}}"></td>
                  
                  @foreach($columns as $c)
                  <td>{{ $d[$c['column_name']] }} <input type="hidden" name="row[{{$i}}][]" value="{{ $d[$c['column_name']] }} "></td>
                  @endforeach
                </tr>
                <?php $i++ ; ?>
              @endforeach
               
                
              </tbody>
            </tbody>
          </table>
         </form>
        </div>
      </div>
    </div>
@stop

@stop
@section('custom-js')

<script>
$(function()
{

  $('#download-excel').click(function(e)
  {
    e.preventDefault();

    var row = $('#excel-form').find('tr');
    var main_data_row = [];
    $(row).each(function()
    {
      var data_row = [];
      var column = $(this).find('input');
      $(column).each(function()
      {
        /////// Student-v1-changes-made-here ///////
        var val = $(this).val();
        data_row.push(val.replace("'", ""));
        $(this).remove();
        /////// Student-v1-changes-made-here ///////
      });

      main_data_row.push(data_row);

    });

    main_data_row = JSON.stringify(main_data_row);

    $('#append-data').html("<input type = 'hidden' name = 'json' value = '" +main_data_row + "'>");

    $('#excel-form').submit();

  });

});
</script>

@stop

