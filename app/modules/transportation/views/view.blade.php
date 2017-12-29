@extends('backend.'.$role.'.main')

  @section('custom-meta')
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

  @stop

  @section('custom-css')
    <!-- Date Picker -->
    <link href="{{asset('sms/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
    
    <!-- Daterange picker -->
    <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
  @stop
  
  @section('content')
  <body onload="load()">

  <div class="row">
    <input type = "hidden" id = "lat" value = "@if($initial_lat_lng) {{$initial_lat_lng->lat}} @else 27.77 @endif">
    <input type = "hidden" id = "lng" value = "@if($initial_lat_lng) {{$initial_lat_lng->lng}} @else 85.77 @endif">

    <div class="col-sm-3 col-sm-offset-9" style="margin-bottom:15px">
      <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right"><i class="fa fa-fw fa-arrow-left btn-flat"></i> Go Back</a>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-12">
          @if($initial_lat_lng)
            <h4 class="text-red">
              Plate no. {{ $initial_lat_lng->number_plate }} 
              <small class="text-green">
                Average speed: {{ $initial_lat_lng->average_speed }},
                Last Updated: {{ HelperController::dateTimePrettyConverter($initial_lat_lng->updated_at) }} 
              </small>
            </h4>                    
            <div id="map" style="width: 500px; height: 300px"></div>
          @else
            <h1>No locations available</h1>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
      <h4 class="text-green">Locate vehicle</h4> 
      <select id="transportation" name="transportation" class="form-control"> 
      
      @foreach($transportation_list as $index => $value)
      <option value="{{URL::route('transportation-view-locations', array($index))}}" @if(URL::current() == URL::route('transportation-view-locations', array($index))) selected @endif>{{$value}}</option>
      @endforeach
    </select>
    </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <label class="control-label">Date Range</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
          </div>
          <input name="date" type="text" class="form-control pull-right" id="daterange"
                  value= "{{ Input::get('daterange') }}"/>
        </div><!-- /.input group -->
      </div><!-- /.form group -->
      <button id="filter_date">Filter</button>

      <table class="table table-striped table-hover table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Distance Covered</th>
            <th>Average Speed</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($distance_log as $log)
            <?php
              $start_date_time = DateTime::createFromFormat('Y-m-d H:i:s', $log->start_date_time);
              $end_date_time = DateTime::createFromFormat('Y-m-d H:i:s', $log->end_date_time);
            ?>
            <tr>
              <td>{{ $start_date_time->format('d F Y') }}</td>
              <td>{{ $start_date_time->format('g:i A') }}</td>
              <td>{{ $end_date_time->format('g:i A') }}</td>
              <td>{{ round((float)$log->total_distance, 3) }} km</td>
              <?php $time_diff = $end_date_time->getTimeStamp() - $start_date_time->getTimeStamp() ?>
              <td>
                @if ($time_diff)
                  {{ round((float)$log->total_distance / $time_diff * 3600) }} km/hr
                @else
                  N/A
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
     
  
  @stop

  @section('custom-js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
  
  <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js') }}" type="text/javascript"></script>

  <script type="text/javascript">
    $(function() {
      $('#daterange').daterangepicker();

      $('#filter_date').click(function() {
        window.location.replace("{{ URL::current() }}" + '?daterange=' + $('#daterange').val());
      });
    });
  </script>

  <script src="https://maps.googleapis.com/maps/api/js?sensor=false&key={{ MAP_API_KEY }}"
            type="text/javascript"></script>
  
    <script type="text/javascript">
    //<![CDATA[

    var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };

    function load() {
      var lat = $('#lat').val();
      var lng = $('#lng').val();

      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(lat, lng),
        zoom: 18,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("{{ BASE_URL }}/transportation/make-xml/{{$unique_transportation_id}}", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");
          var address = markers[i].getAttribute("address");
          var type = markers[i].getAttribute("type");
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
          var html = "<b>" + name + "</b> <br/>" + address;
          var icon = customIcons[type] || {};
          var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: icon.icon
          });
          bindInfoWindow(marker, map, infoWindow, html);
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //]]>

  </script>    

<script type="text/javascript">
    $(document).on('change', '#transportation', function() {
      var unique_transportation_id = $('#transportation').val();
      window.location = $(this).val();
    })

    
  </script>

  @stop
