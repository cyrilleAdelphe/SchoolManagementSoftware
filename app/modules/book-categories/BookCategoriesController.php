<?php
class BookCategoriesController extends BaseController
{
	protected $view = 'book-categories.views.';

	protected $model_name = 'BookCategories';

	protected $module_name = 'book-categories';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'title',
										'alias'			=> 'Category Title'
									),
									array
									(
										'column_name' 	=> 'rack_number',
										'alias'			=> 'Rack Number '
									),
									array
									(
										'column_name' 	=> 'description',
										'alias'			=> 'Description'
									),
								);

	public function postCreateView()
	{

		/*$input = Input::all();
		echo '<pre>';
		print_r($input);
		die();*/

		AccessController::allowedOrNot($this->module_name, 'can_create');
		/**
		 * The class_range input is broken down to individual classes
		 */
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		/*echo '<pre>';
		print_r($data);
		die();*/
		$result = $this->validateInput($data);

		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}

		/*if($data['class_range'])
		{
			$class_range_array = explode('-', $data['class_range']);
			$data['from_class'] = $class_range_array[0];
			$data['to_class'] = $class_range_array[1];
		}
		else
		{
			$data['from_class'] = 0;
			$data['to_class']	= 0;
		}*/

		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->storeInDatabase($data);

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
}
?>