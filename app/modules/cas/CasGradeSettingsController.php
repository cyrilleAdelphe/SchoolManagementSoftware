<?php

	class CasGradeSettingsController extends BaseController
	{


		protected $view = 'cas.views.grade-settings.';
		protected $model_name = 'CasGradeSettings';
		protected $module_name = 'cas';

		public $current_user;

		public function getGradeSettingsListView()
		{

			return View::make($this->view.'list');
		}

		public function postGradeSettingsCreatePost()
		{
			$input = Input::all();


			$class_ids = $this->getClassList($input['from_class'], $input['to_class'], $input['session']);

			try
			{
				DB::connection()->getPdo()->beginTransaction();
				CasGradeSettings::where('academic_session_id', $input['session'])->whereIn('class_id', $class_ids)->delete();
				foreach($class_ids as $c)
				{
					
					foreach($input['from'] as $index => $from)
					{
						$data = [];
						$data['class_id'] = $c;
						$data['academic_session_id'] = $input['session'];
						$data['from_percent'] = $input['from'][$index];
						$data['to_percent'] = $input['to'][$index];
						$data['grade'] = $input['grade'][$index];
						$data['grade_point'] = $input['grade_point'][$index];
						$data['is_active'] = 'yes';
						$this->storeInDatabase($data, 'CasGradeSettings');
					}
				}

				DB::connection()->getPdo()->commit();
				Session::flash('success-msg', 'Successfully stored');
				return Redirect::back();
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
				return Redirect::back();
			}
			
		}

		private function getClassList($start, $end, $session_id)
		{
			$classes = Classes::whereIn('id', [$start, $end])
								->lists('sort_order');

			if(count($classes))
			{
				$classes[1] = isset($classes[1]) ? $classes[1] : $classes[0];
				if($classes[0] > $classes[1])
				{
					$temp = $classes[0];
					$classes[0] = $classes[1];
					$classes[1] = $temp;
				}

				$classes = Classes::whereBetween('sort_order', $classes)
									->where('academic_session_id', $session_id)
									->where('is_active', 'yes')
									->lists('id');	
			}

			

			return $classes;
		}

		public function getEditGradeSettingsView()
		{

		}

		public function postEditGradeSettingsView()
		{
			$input = Input::all();

			/*echo '<pre>';
			print_r($input);
			die();*/
			$class_ids = $input['class_id'];

			try
			{
				DB::connection()->getPdo()->beginTransaction();
				CasGradeSettings::where('academic_session_id', $input['session'])->whereIn('class_id', $class_ids)->delete();
				foreach($class_ids as $c)
				{
					
					foreach($input['from'] as $index => $from)
					{
						$data = [];
						$data['class_id'] = $c;
						$data['academic_session_id'] = $input['session'];
						$data['from_percent'] = $input['from'][$index];
						$data['to_percent'] = $input['to'][$index];
						$data['grade'] = $input['grade'][$index];
						$data['grade_point'] = $input['grade_point'][$index];
						$data['is_active'] = 'yes';
						$this->storeInDatabase($data, 'CasGradeSettings');
					}
				}

				DB::connection()->getPdo()->commit();
				Session::flash('success-msg', 'Successfully Edited');
				return ['status' => 'success', 'msg' => 'Successfully Edited'];
				
			}
			catch(Exception $e)
			{
				DB::connection()->getPdo()->rollback();
				Session::flash('error-msg', $e->getMessage());
				return ['status' => 'error', 'msg' => $e->getMessage()];
				
			}


		}

		//////////// these are for apis
		public function apiGetGradeSettingsListFromClassToClass()
		{
			//return 'hello';
			$input = Input::all();
			$data = $this->apiGetGradeFromClass($input);

			return view::make('cas.views.partials.grade-settings.grade-settings-list-from-class-to-class')
					->with('data', $data)
					->with('session', $input['session']);
		}

		public function apiGetGradeFromClass($input = [])
		{

			$class_ids = $this->getClassList($input['from_class'], $input['to_class'], $input['session']);
			$data = CasGradeSettings::whereIn('class_id', $class_ids)
									->orderBy('class_id', 'ASC')
									->orderBy('from_percent', 'DESC')
									->get();

			
			$return_data = [];
			foreach($data as $d)
			{
				$return_data[$d->class_id][] = ['grade' => $d->grade, 'grade_point' => $d->grade_point, 'from_percent' => $d->from_percent, 'to_percent' => $d->to_percent];
			}

			unset($data);
			
			//if(count($return_data) > 1)
			//{
				foreach($return_data as $class_id => $data)
				{
					foreach($return_data as $class_id_to_compare => $d)
					{
						
						
						
						if($class_id != $class_id_to_compare && isset($return_data[$class_id_to_compare]) && isset($return_data[$class_id]))
						{
							if($return_data[$class_id] == $return_data[$class_id_to_compare])
							{
								$data_to_return[$class_id]['classes'][] = $class_id_to_compare;
								$data_to_return[$class_id]['data'] = $data;
								unset($return_data[$class_id_to_compare]);
							}
						}
					}
				}	
			//}

			foreach($return_data as $class_id => $data)
			{
				if(!isset($data_to_return[$class_id]))
				{
					$data_to_return[$class_id]['classes'] = [];
					$data_to_return[$class_id]['data'] = $data;
				}
			}

			ksort($data_to_return);
			
			return $data_to_return;
			
		}
	}