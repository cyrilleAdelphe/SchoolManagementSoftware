<?php

class GuardianHelperController
{
	public function htmlAjaxSearchStudents($data)
	{
		$html = '';
		if($data['status'] == 'error')
		{
			$html .= '<div class = "row"><div class = "col-md-12"><h2>'.$data['msg'].'</h2></div></div>';
		}
		else
		{
			if(count($data['data']))
			{
				foreach($data['data'] as $d)
				{
					$html .= '<div class = "row">';
					$html .= '<div class = "col-md-1"><input type = "checkbox" name = "student_id[]" value = "'.$d->id.'">'.$d->id.'</div>';
					$html .= '<div class = "col-md-3">'.$d->student_name.'</div>';
					$html .= '<div class = "col-md-2">'.$d->class_name.'</div>';
					$html .= '<div class = "col-md-6"><img width = "100px" height = "100px" class = "img-responsive lead-img" src = "'.Config::get('app.url').'app/modules/student/assets/images/'.$d->photo.'"></div>';
					$html .= '</div>';
				}
			}
			else
			{
				$html .= '<div class = "row"><div class = "col-md-12"><h2>No Students Found</h2></div></div>';
			}
		}

		return $html;
	}
}