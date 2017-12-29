<?php
class EventsHelper
{
	public static function convertToRange($from,$to) 
	{
		return DateTime::createFromFormat('Y-m-d H:i:s',$from)->format('m/d/Y g:i A') . ' - ' . DateTime::createFromFormat('Y-m-d H:i:s',$to)->format('m/d/Y g:i A');
	}

	public static function getAllEvents() 
	{
		$events = Events::where('is_active','yes')->get();
		$events_array = array();
		foreach($events as $event)
		{
			switch ($event['event_type']) 
			{
				case 'holiday':
					$background_color = '#f56954'; //red
					$border_color = '#f56954'; //red
					break;

				case 'exam':
					$background_color = 'rgb(0,0,200)'; //green
					$border_color = '#f56954'; //red
					break;

				case 'school_function':
					$background_color = 'rgb(0,200,0)'; //green
					$border_color = 'rgb(0,200,0)'; //red
					break;
				
				default:
					$background_color = '#f56954'; //red
					$border_color = '#f56954'; //red
					break;
			}
			
			$events_array[] = array(
											'title'				=> $event['title'] . 
																	' (Nepali: '. substr($event['from_bs'],0,10) .
																	' to '. substr($event['to_bs'],0,10) . ')',

											'start'				=> $event['from_ad'],
											'end'				=> $event['to_ad'],
											'backgroundColor'	=> $background_color,
              								'borderColor'		=> $border_color

										);
		}
		return $events_array;
	}
}