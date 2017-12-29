<?php

class GradeHelperController {

	/*public static function convertPercentageToGrade($percentage) {
		if (File::exists(GRADE_CONFIG_FILE)) {
			$config = json_decode(File::get(GRADE_CONFIG_FILE));
			foreach($config as $grade) {
				if ( $percentage >= $grade->from && $percentage < $grade->to ) {
					return $grade->grade;
				}
			}
			// if percentage is off the charts ;)
			return $grade->grade;
			
		} else {
			return 'N/A';
		}
	}*/

	public static function convertPercentageToGradePoint($percentage) {
		if (File::exists(GRADE_CONFIG_FILE)) {
			$config = json_decode(File::get(GRADE_CONFIG_FILE));
			foreach($config as $grade) {
				if ( $percentage >= $grade->from && $percentage < $grade->to ) {
					return $grade->grade_point;
				}
			}
			// if percentage is off the charts ;)
			return $grade->grade_point;
		} else {
			return 'N/A';
		}
	}

	

	public static function convertPercentageToGrade($session_id, $class_id, $marks_in_percentage)
	{

		$marks_in_percentage = $marks_in_percentage > 100 ? 100 : $marks_in_percentage;
		$grade = CasGradeSettings::where('academic_session_id', $session_id)
								->where('class_id', $class_id)
								->where('from_percent', '<=', $marks_in_percentage);

		if($marks_in_percentage >= 100)
		{
			$grade = $grade->where('to_percent', '>=', $marks_in_percentage);
		}
		else
		{
			$grade = $grade->where('to_percent', '>', $marks_in_percentage);
		}

		$grade = $grade->first();		
		
		

		return $grade;
	}

	public static function getCasNonCasGrade($cas_subject_grade_point, $non_cas_grade_point)
	{
		$config = json_decode(File::get(REPORT_CONFIG_FILE));
		$sum_grade_point = $non_cas_grade_point;
		if($config->cas_percentage > 0 && $cas_subject_grade_point != 'NA')
		{
			$sum_grade_point = ($config->cas_percentage / 100) * $cas_subject_grade_point + ((100 - $config->cas_percentage) / 100 * $non_cas_grade_point);
		}

		return $sum_grade_point;

	}

	public static function getCasGradeSettings($session_id, $class_id)
	{
		$grade = CasGradeSettings::where('academic_session_id', $session_id)
								->where('class_id', $class_id)
								->orderBy('from_percent', 'DESC')
								->get();

		return $grade;
	}

	public static function convertGradeToFrom($session_id, $class_id)
	{
		$grades = CasGradeSettings::where('academic_session_id', $session_id)
								->where('class_id', $class_id)
								->orderBy('grade_point', 'ASC')
								->lists('grade_point', 'grade');

		$return_data = [];
		$status = true;
		$data = [];
		$status = true;
		foreach($grades as $grade => $grade_point)
		{
			
			if($status)
			{
				$data['from'] = $grade_point;
				$data['grade'] = $grade;
				$status = false;
			}
			else
			{
				$data['to'] = $grade_point;
				$return_data[] = $data;
				$data = [];
				$data['from'] = $grade_point;
				$data['grade'] = $grade;
			}
		}

		$data['to'] = $grade_point;
		$return_data[] = $data;

		return array_reverse($return_data);
		
	}

	public static function convertGradePointToGrade($data, $gpa)
	{
		
		foreach($data as $d)
		{
			if($gpa >= $d['from'])
			{
				return $d['grade'];
			}
		}
	}

}