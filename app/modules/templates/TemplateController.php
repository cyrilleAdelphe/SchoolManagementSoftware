<?php

class TemplateController extends BaseController
{
	protected $view = 'templates.views.';

	protected $model_name = 'Template';

	protected $module_name = 'templates';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'template_name',
										'alias'			=> 'Template Name'
									),
									array
									(
										'column_name' 	=> 'template_alias',
										'alias'			=> 'Template Alias'
									),
									array
									(
										'column_name' 	=> 'position_name',
										'alias'			=> 'Position'
									),
									array
									(
										'column_name' 	=> 'sort_order',
										'alias'			=> 'Sort Order'
									)
								 );

	public function getCreateView()
	{
		$helper = new TemplateHelperController;
		$positions = $helper->getAllPositions();

		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('positions', $positions)
					->with('actionButtons', $this->getActionButtons());
	}

	public function postCreateView()
	{
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data);

		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();
				$dataToStoreInTemplate = array();
				
				$dataToStoreInTemplate['template_name'] = $data['template_name'];
				$dataToStoreInTemplate['template_alias'] = $data['template_alias'];
				$dataToStoreInTemplate['is_active'] = $data['is_active'];

				$id = $this->storeInDatabase($dataToStoreInTemplate);

				if(isset($data['position_id']))
				{
					$dataToStoreInPositionTemplate = array();
					$dataToStoreInPositionTemplate['template_id'] = $id;
					$dataToStoreInPositionTemplate['is_active'] = $data['is_active'];

					foreach($data['position_id'] as $index => $position_id)
					{
						$dataToStoreInPositionTemplate['position_id'] = $position_id;
						$dataToStoreInPositionTemplate['sort_order'] = $data['sort_order'][$index];
						$this->storeInDatabase($dataToStoreInPositionTemplate, 'PositionTemplate');
					}
				}

				$success = true;
				$msg = 'Record successfully created';
				$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function getEditView($id)
	{

		$model = new $this->model_name;
		$helper = new TemplateHelperController;

		$data = $model->getEditViewData($id);
		$selected_positions = $helper->getSelectedPositions($id); //this is template id
		$positions = $helper->getAllPositions();

		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('data', $data)
					->with('selected_positions', $selected_positions)
					->with('positions', $positions)
					->with('actionButtons', $this->getActionButtons());
	
	}

	public function postEditView()
	{
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();

		$result = $this->validateInput($data, true);

		if($result['status'] == 'error')
		{
			return Redirect::route($this->module_name.'-edit-get', array($data['id']))
						->withInput()
						->with('errors', $result['data']);
		}
		
		try
		{

			DB::connection()->getPdo()->beginTransaction();

				$dataToUpdateInTemplate = array();
				
				$dataToUpdateInTemplate['template_name'] = $data['template_name'];
				$dataToUpdateInTemplate['template_alias'] = $data['template_alias'];
				$dataToUpdateInTemplate['is_active'] = $data['is_active'];
				$dataToUpdateInTemplate['id'] = $data['id'];

				$id = $this->updateInDatabase($dataToUpdateInTemplate);

				if(isset($data['position_id']))
				{
					$dataToStoreInPositionTemplate = array();
					$dataToStoreInPositionTemplate['template_id'] = $id;
					$dataToStoreInPositionTemplate['is_active'] = $data['is_active'];

					PositionTemplate::where('template_id', $id)->delete();

					foreach($data['position_id'] as $index => $position_id)
					{
						$dataToStoreInPositionTemplate['position_id'] = $position_id;
						$dataToStoreInPositionTemplate['sort_order'] = $data['sort_order'][$index];
						$this->storeInDatabase($dataToStoreInPositionTemplate, 'PositionTemplate');
					}
				}

				$success = true;
				$msg = 'Record successfully updated';
				$param['id'] = $id; 

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
			$param['id'] = $data['id'];
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}

	public static function getTemplate($position_name)
	{
		//get all active templates
		$model = 'Template';
		$activeTemplates =  DB::table($model::getTableName())
							->join(PositionTemplate::getTableName(), PositionTemplate::getTableName().'.template_id', '=', $model::getTableName().'.id')
							->join(Position::getTableName(), Position::getTableName().'.id', '=', PositionTemplate::getTableName().'.position_id')
							->select($model::getTableName().'.template_name')
							->where($model::getTableName().'.is_active', 'yes')
							->where(Position::getTableName().'.is_active', 'yes')
							->where(PositionTemplate::getTableName().'.is_active', 'yes')
							->where('position_name', $position_name)
							->orderBy('sort_order', 'ASC')
							->get();

		$return = array();
		foreach($activeTemplates as $a)
		{
		 	$return[] = TemplateController::getTemplateFile($a->template_name);
		}

		$return = implode("\n", $return);

		return $return;
	}

	public static function getTemplateFile($filename)
	{
		return View::make('templates.views.files.'.$filename);
	}
}
