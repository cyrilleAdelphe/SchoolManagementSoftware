<?php

class SessionMigrateController extends BaseController
{
	public $current_session;
	public $previous_session;
	public $new_classes = [];//['old_id' => 'new_id'];
	public $new_subjects = []; //['old_id' => 'new_id'];

	public function getPreviousClassess()
	{
		$previous_classes = DB::table(Classes::getTableName())->where('academic_session_id', $this->previous_session)
		->get();

		$new_classes = [];

		foreach($previous_classes as $p)
		{
			$data_to_store = [];

			foreach($p as $index => $value)
			{
				$data_to_store[$index] = $value;
			}

			unset($data_to_store['created_at']);
			unset($data_to_store['updated_at']);
			unset($data_to_store['id']);

			$data_to_store['academic_session_id'] = $this->current_session;

			$new_classes[$p->id] = Classes::firstOrCreate($data_to_store)->id;
		}

		echo '<pre>';
		print_r($new_classes);

		$this->new_classes = $new_classes;
	}

	public function getPreviousClassesSections()
	{
		$previous_class_sections = DB::table(ClassSection::getTableName())->whereIn('class_id', array_keys($this->new_classes))
												->get();

		foreach($previous_class_sections as $p)
		{
			$data_to_store = [];
			foreach($p as $index => $value)
			{
				$data_to_store[$index] = $value;
			}

			unset($data_to_store['created_at']);
			unset($data_to_store['updated_at']);
			unset($data_to_store['id']);

			$data_to_store['class_id'] = $this->new_classes[$data_to_store['class_id']];

			ClassSection::firstOrCreate($data_to_store);
		}
	}

	public function getPreviousSubjects()
	{
		$previous_subjects = DB::table(Subject::getTableName())->whereIn('class_id', array_keys($this->new_classes))
									->get();

		foreach($previous_subjects as $p)
		{
			$data_to_store = [];
			foreach($p as $index => $value)
			{
				$data_to_store[$index] = $value;
			}

			unset($data_to_store['created_at']);
			unset($data_to_store['updated_at']);
			unset($data_to_store['id']);

			$data_to_store['class_id'] = $this->new_classes[$data_to_store['class_id']];

			$new_subjects[$p->id] = Subject::firstOrCreate($data_to_store)->id;
		}

		print_r($new_subjects);
		$this->new_subjects = $new_subjects;
	}

	public function assignTeachers()
	{
		$previous = DB::table(Teacher::getTableName())->whereIn('class_id', array_keys($this->new_classes))
							->where('session_id', $this->previous_session)
							->get();

		foreach($previous as $p)
		{
			$data_to_store = [];
			foreach($p as $index => $value)
			{
				$data_to_store[$index] = $value;
			}

			unset($data_to_store['created_at']);
			unset($data_to_store['updated_at']);
			unset($data_to_store['id']);

			$data_to_store['class_id'] = $this->new_classes[$data_to_store['class_id']];
			$data_to_store['session_id'] = $this->current_session;

			$new_subjects[$p->id] = Teacher::firstOrCreate($data_to_store)->id;
		}
	}

	public function assignDailyRoutine()
	{
		$previous = DB::table(DailyRoutine::getTableName())->whereIn('class_id', array_keys($this->new_classes))
							->where('session_id', $this->previous_session)
							->get();

		foreach($previous as $p)
		{
			$data_to_store = [];
			foreach($p as $index => $value)
			{
				$data_to_store[$index] = $value;
			}

			unset($data_to_store['created_at']);
			unset($data_to_store['updated_at']);
			unset($data_to_store['id']);

			$data_to_store['class_id'] = $this->new_classes[$data_to_store['class_id']];
			$data_to_store['session_id'] = $this->current_session;

			DailyRoutine::firstOrCreate($data_to_store);
		}

	}

	public function updateBillingFeeStudent()
	{
		foreach($this->new_classes as $old_class => $new_class)
		{
			BillingFeeStudent::where('class_id', $old_class)
									->update(['class_id' => $new_class]);
		}
	}

	public function postMigrateSession()
	{
		AccessController::allowedOrNot('academic-session', 'can_migrate');
		//check if current session is current or not
		$this->current_session = Input::get('current_session');
		$this->previous_session = Input::get('previous_session');
		
		if($this->current_session != HelperController::getCurrentSession())
		{
			die('The session is not set as current');
		}
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();
				$this->getPreviousClassess();
				$this->getPreviousClassesSections();
				$this->getPreviousSubjects();
				$this->assignTeachers();
				$this->assignDailyRoutine();
				$this->updateBillingFeeStudent();
			DB::connection()->getPdo()->commit();	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			die();
		}

		Session::flash('success-msg', 'Session Migrated Successfully');
		return Redirect::route('academic-session-list');
	}

	public function getMigrateSession()
	{
		AccessController::allowedOrNot('academic-session', 'can_migrate');
		return View::make('academic-session.views.migrate-session')
					->with('module_name', 'academic-session');
	}
}