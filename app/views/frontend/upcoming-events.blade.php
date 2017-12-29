<?php 
$current_session = AcademicSession::where('is_current', 'yes')
																	->first();
$upcoming_events = $current_session ?
											Events::where('from_ad', '>=', $current_session->session_start_date_in_ad)
														->where('from_ad', '<=', $current_session->session_end_date_in_ad)
														->where('from_ad', '>=', date('Y-m-d'))

														->orderBy('from_ad', 'ASC')
														->take(5)
														->get() :
											array();

?>
    <div class="fTitle"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Upcoming Events</div>
    <table class="table  table-striped  table-hover">
      <tbody>
      	@foreach($upcoming_events as $event)
          <tr>
            <td>

              <div class="day">
              	<?php 
              	$start_date = DateTime::createFromFormat('Y-m-d H:i:s', $event->from_ad);
              	$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $event->to_ad);
                $pretty_date = HelperController::dateTimePrettyConverter($event->from_ad);
                $pretty_date_array = explode(' ', $pretty_date);
                
                $day = isset($pretty_date_array[0]) ? $pretty_date_array[0] : '';
                $month = isset($pretty_date_array[1]) ? $pretty_date_array[1] : '';
              	?>
                
                <div style="font-size: 20px;">{{ $day }}</div>
                {{ $month }}
              
              </div>
            </td>
            <td>
               <div class="event-title"> {{ $event->title }}  </div>            
               {{ $event->description }}
                <div class="time">
                  <i class="fa fa-fw fa-clock-o"></i> {{ $pretty_date }} to {{ HelperController::dateTimePrettyConverter($event->to_ad) }}
                </div>
            </td>
          </tr>
        @endforeach                    
      </tbody>
    </table>

						