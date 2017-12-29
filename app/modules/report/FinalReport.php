<?php
class FinalReport extends BaseModel
{
	protected $table = 'final_reports';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'FinalReport';

	public $createRule = [
							'exam_id'	=>	['required'],
							'subject_id'=>	['required'],
							'duration'	=>	['required'],
							'pass_marks' 				=> ['required', 'integer'],
							'full_marks' 				=> ['required', 'integer'],
							'start_date_in_ad'					=> ['required','date'],
							//'end_date_in_ad'					=> ['required', 'date'],
							'duration'			=> ['required']
						];

	public $updateRule = [
							'exam_id'	=>	['required'],
							'subject_id'=>	['required'],
							'duration'	=>	['required'],
							'pass_marks' 				=> ['required', 'integer'],
							'full_marks' 				=> ['required', 'integer'],
							'start_date_in_ad'					=> ['required','date'],
							//'end_date_in_ad'					=> ['required', 'date'],
							'duration'			=> ['required']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$data = array();
		$total_results_data = array();
		$input = Input::all();
		$class_id = isset($input['class_id']) ? $input['class_id'] : 0;
		$section_id = isset($input['section_id']) ? $input['section_id'] : 0;
		$exam_id = isset($input['exam_id']) ? $input['exam_id'] : 0;

		if($class_id && $section_id && $exam_id)
		{
			$result = $model::where('exam_id', $exam_id)
							->where('is_active', 'yes')
							->select(array('subject_id', 'pass_marks', 'full_marks', 'remarks', 'start_date_in_ad', 'duration'))
							->get();

			foreach($result as $r)
			{
				$data[$r->subject_id] = array('pass_marks' => $r->pass_marks, 'full_marks' => $r->full_marks, 'remarks' => $r->remarks, 'start_date_in_ad' => $r->start_date_in_ad, 'duration' => $r->duration);
			}

			//unlink($result);

			$total_subjects = Subject::where('section_id', $section_id)
									  ->where('class_id', $class_id)
									  ->select(array('subject_name', 'id', 'pass_marks', 'full_marks'))
									  ->get();

			foreach($total_subjects as $t)
			{
				$total_results_data[$t->id]	= array('subject_name' => $t->subject_name, 'pass_marks' => $t->pass_marks, 'full_marks' => $t->full_marks) ;
			}

			$last_updated_data = FinalReport::getLastUpdated($condition = array(array('field_name' => 'is_active', 'operator' => '=', 'compare_value' => 'yes')), $fields_required = array('updated_by', 'id'), 'Report');

			//unlink($total_subjects);
		
		}

		return array('data' => $data, 'total_results_data' => $total_results_data, 'query' => $input, 'last_updated_data' => $last_updated_data);
	}
}