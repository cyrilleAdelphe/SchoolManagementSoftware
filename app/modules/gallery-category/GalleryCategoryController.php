<?php

class GalleryCategoryController extends BaseController
{
	protected $view = 'gallery-category.views.';

	protected $model_name = 'GalleryCategory';

	protected $module_name = 'gallery-category';

	public $current_user;

	public $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'title',
										'alias'			=> 'Title'
									),
									array
									(
										'column_name' 	=> 'description',
										'alias'			=> 'Description'
									)
								 );

	public function showAll()
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$model = $this->model_name;
		$categories = $model::where('is_active', 'yes')
			->select('id', 'title', 'description')
			->get();

		foreach ($categories as $category)
		{
			try
			{
				$category->image = 
					Config::get('app.url') .
					'app/modules/gallery/assets/images/thumbnails/' .
					
					Gallery::where('category_id', $category->id)
						->orderBy(DB::raw('RAND()'))
						->select('id')
						->firstOrFail()
						->id;
			}
			catch (Exception $e)
			{
				$category->image = Config::get('app.url') .
					'app/modules/student/assets/images/no-img.png';
			}
		}

		return View::make($this->view . 'show-all')
			->with('categories', $categories);
	}

	public function getListView()
	{
		AccessController::allowedOrNot('gallery-category', 'can_view');
		$model = new $this->model_name;
		$queryString = $this->getQueryString();
		$data = $model->getListViewData($queryString);

		$searchColumns = $this->getSearchColumns();
		$tableHeaders = $this->getTableHeader();
		//$actionButtons = $this->getActionButtons();
		$queries = $this->getQueries();

		return View::make($this->view.'list')
					->with('module_name', $this->module_name)
					->with('current_user', $this->current_user)
					->with('data', $data)
					->with('queries', $queries)
					->with('queryString', Input::query())
					->with('searchColumns', $searchColumns)
					->with('tableHeaders', $tableHeaders)
					->with('paginateBar', $this->getPaginateBar())
					//->with('actionButtons', $actionButtons)
					->with('role', $this->role);

	}

	public function postDelete()
	{
		AccessController::allowedOrNot('gallery-category', 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			$record->delete();
			Session::flash('success-msg', 'Delete Successful');
		}
		else
		{
			Session::flash('error-msg', 'Invalid delete!!!');
		}
		return Redirect::back();
	}

}