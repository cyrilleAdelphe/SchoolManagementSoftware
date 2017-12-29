<?php
class RoutineController extends BaseController
{
	protected $view = 'routine.views.';

	protected $model_name = 'Routine';

	protected $module_name = 'routine';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'title',
										'alias'			=> 'Title'
									),
									array
									(
										'column_name' 	=> 'author',
										'alias'			=> 'Author'
									),
									array
									(
										'column_name' 	=> 'published_date',
										'alias'			=> 'Published Date'
									),
									array
									(
										'column_name' 	=> 'created_at',
										'alias'			=> 'Book registered at'
									),
									array
									(
										'column_name' 	=> 'category_title',
										'alias'			=> 'Category'
									),
									array
									(
										'column_name' 	=> 'price',
										'alias'			=> 'Price'
									),
									array
									(
										'column_name' 	=> 'no_of_copies',
										'alias'			=> 'No. of copies'
									),
									array
									(
										'column_name' 	=> 'in_routine',
										'alias'			=> 'In'
									),
									array
									(
										'column_name' 	=> 'out_routine',
										'alias'			=> 'Out'
									),
									array
									(
										'column_name' 	=> 'max_holding_days',
										'alias'			=> 'Maximum Holding Days'
									),
									array
									(
										'column_name' 	=> 'description',
										'alias'			=> 'Description'
									),

								 );

	public function getCreateView()
	{
		AccessController::allowedOrNot('routine', 'can_create');
		$categories = DB::table(BookCategories::getTableName())
				  ->where('is_active', 'yes')
				  ->select('id', 'title')
				  ->lists('title', 'id');
		
		return View::make($this->view . 'create')
				->with('categories',$categories)
				->with('module_name', $this->module_name)
				->with('current_user', $this->current_user)
				->with('role',$this->role)
				->with('actionButtons', $this->getActionButtons());
	}

	public function postCreateView()
	{
		AccessController::allowedOrNot('routine', 'can_create');
		/*
		 * Some confusing variables / indexes / table fields
		 * index 'book_ids' : ids of the individual copies of the book
		 * field 'book_id': unique id field of the table book_copies (not the primary key)
		 * variable $routine_id: id of the book from the table routine
		 */
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data);

		$book_ids_array = array();
		// if we know that we have a valid non-empty string of book_ids, check if each is unique
		if(!empty($data['book_ids']) && ($result['status']!='error' || !$result['data']->has('book_ids')) )
 		{
 			$data['book_ids'] = HelperController::csvRangeToCsv($data['book_ids']);
 			$book_ids_array = array_unique(explode(',', $data['book_ids']));
 			$result_book_ids = BookCopiesHelper::validateIds($book_ids_array); // validates the uniqueness of each id
 			if($result_book_ids['status']=='error')
 			{
 				$result['status'] = 'error';
 				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;
 				// only non_unique_book_ids error can occur after the first validation from validateInput method
 				if($result_book_ids['data']->has('non_unique_book_ids'))
 				{
 					$result['data']->getMessageBag()->add('non_unique_book_ids',$result_book_ids['data']->first('non_unique_book_ids'));
 				}
 			}
 		}
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		if(!empty($data['book_ids']))
		{
			$data['no_of_copies'] = sizeof($book_ids_array);
		}

		if(Input::hasFile('image'))
		{
			$image_controller = new FileUploadController2(Input::file('image'));
			
			$data['image'] = $image_controller->saveImage($strict = false, (base_path().'/public/sms/assets/img/routine'));
			$image_controller->generateThumbnail($aspect_ratio = false, $resize_parameter = array('height' =>200, 'width' => 150), (base_path().'/public/sms/assets/img/routine/small'), $data['image']);
			$image_controller->generateThumbnail($aspect_ratio = true, $resize_parameter = array('compression_ratio' => 2), (base_path().'/public/sms/assets/img/routine/medium'), $data['image']);
		}
		else
		{
			$data['image'] = '';
		}
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->storeInDatabase($data);

			if(!empty($data['book_ids']))
			{
				foreach ($book_ids_array as $book_id) 
				{
					BookCopies::create([
										'routine_id' 	=> $id, // id of Routine Model
										'book_id'	=> $book_id // id of the copy of the book
									]);
				}

			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function getEditView($id)
	{
		AccessController::allowedOrNot('routine', 'can_edit');
		$categories = DB::table(BookCategories::getTableName())
				  ->where('is_active', 'yes')
				  ->select('id', 'title')
				  ->lists('title', 'id');

		$book_ids = BookCopiesHelper::getBookCopiesIdCsv($id);

		$model = $this->model_name;
		$model = new $model;
		$data = $model->getEditViewData($id);
		
		return View::make($this->view.'edit')
					->with('categories',$categories)
					->with('book_ids',$book_ids)
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data);
					//->with('actionButtons', $this->getActionButtons());
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot('routine', 'can_edit');
		/*
		 * Some confusing variables / indexes / table fields
		 * index 'book_ids' : ids of the individual copies of the book
		 * field 'book_id': unique id field of the table book_copies (not the primary key)
		 * variable $routine_id: id of the book from the table routine
		 */

		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data,true);

		$book_ids_array = array();
		// if we know that we have a valid non-empty string of book_ids, check if each is unique
		if(!empty($data['book_ids']) && ($result['status']!='error' || !$result['data']->has('book_ids')) )
 		{
 			$data['book_ids'] = HelperController::csvRangeToCsv($data['book_ids']);
 			$book_ids_array = explode(',', $data['book_ids']);
 			//the ids that are already associated to the current routine_id
 			$except_array = array();
 			$except = BookCopies::select('book_id')->where('routine_id',$id)->get();
 			foreach($except as $existing) $except_array[] = $existing['book_id'];
 			
 			$new_ids = array_diff($book_ids_array , $except_array);

 			$result_book_ids = BookCopiesHelper::validateIds($new_ids); // validates the uniqueness of each id
 			if($result_book_ids['status']=='error')
 			{
 				$result['status'] = 'error';
 				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;
 				// only non_unique_book_ids error can occur after the first validation from validateInput method
 				if($result_book_ids['data']->has('non_unique_book_ids'))
 				{
 					$result['data']->getMessageBag()->add('non_unique_book_ids',$result_book_ids['data']->first('non_unique_book_ids'));
 				}
 			}
 		}
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-edit-get',[$id])
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		if(!empty($data['book_ids']))
		{
			$data['no_of_copies'] = sizeof($book_ids_array);
		}

		if(Input::hasFile('image'))
		{
			$image_controller = new FileUploadController2(Input::file('image'));
			
			$data['image'] = $image_controller->saveImage($strict = false, (base_path().'/public/sms/assets/img/routine'));
			$image_controller->generateThumbnail($aspect_ratio = false, $resize_parameter = array('height' =>200, 'width' => 150), (base_path().'/public/sms/assets/img/routine/small'), $data['image']);
			$image_controller->generateThumbnail($aspect_ratio = true, $resize_parameter = array('compression_ratio' => 2), (base_path().'/public/sms/assets/img/routine/medium'), $data['image']);
		}
		else
		{
			$data['image'] = Routine::find($id)['image'];
		}
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->updateInDatabase($data);

			BookCopies::where('routine_id',$id)->delete();

			if(!empty($data['book_ids']))
			{
				foreach ($book_ids_array as $book_id) 
				{
					BookCopies::create([
										'routine_id' 	=> $id, // id of Routine Model
										'book_id'	=> $book_id // id of the copy of the book
									]);
				}

			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}
}