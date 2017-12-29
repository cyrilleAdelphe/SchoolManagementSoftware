<?php
class EthnicityController extends BaseController
{

	protected $module_name = 'ethnicity';
 	protected $model_name = 'Ethnicity';
 	protected $view = 'ethnicity.views.';


 	public function getEthnicityView()
 	{
 	
 		AccessController::allowedOrNot($this->module_name, 'can_view');
 		$ethnicity_list = Ethnicity::orderBy('id','DESC')->get();
 		return View::make($this->view.'list-ethnicity')
 						->with('ethnicity_list', $ethnicity_list);
 	}

 	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		

		$model = new $this->model_name;
		$ethnicity_data = $model->getCreateViewData();
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)			
					->with('ethnicity_data', $ethnicity_data);
	}


 	public function postCreateEthnicity() {

 		AccessController::allowedOrNot($this->module_name, 'can_create');

 		$model = new Ethnicity;
 		
 		$validator = Validator::make(Input::all(), $model->rules);

 		if($validator->fails())
 		{
 			return Redirect::back()
 							->withInput()
 							->withErrors($validator);
 		}

 		$ethnicity = new Ethnicity;
 		$ethnicity->ethnicity_name = Input::get('ethnicity_name');
 		$ethnicity->ethnicity_code = Input::get('ethnicity_code');
 		$ethnicity->is_active 	   = Input::get('is_active');
 		$ethnicity->save();

 		Session::flash('success-msg', 'Ethnicity created successfully');
 		return Redirect::back();

 	}

 	public function getEthnicityEdit($id)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		$model = new $this->model_name;
 		$ethnicity = Ethnicity::find($id);
 		$data = $model->getEditViewData($id);	
 		$ethnicity_data = DB::table('ethnicity')->select('ethnicity_name','id')->where('is_active', 'yes')->lists('ethnicity_name', 'id');

 		return View::make($this->view. 'edit-ethnicity')
 								->with('ethnicity', $ethnicity)
 								->with('ethnicity_data', $ethnicity_data);;
 	}

 
 	public function postEthnicityUpdate($id)
 	{
 		
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		$ethnicity = Ethnicity::find($id);
 		$ethnicity->ethnicity_name = Input::get('ethnicity_name');
 		$ethnicity->ethnicity_code = Input::get('ethnicity_code');
 		$ethnicity->is_active 	   = Input::get('is_active');
 		$ethnicity->save();

 		Session::flash('success-msg', 'Ethnicity edited successfully');
 		return Redirect::route('ethnicity-list');

 	}

 	public function deleteEthnicity($id)

 	{
 		AccessController::allowedOrNot($this->module_name, 'can_delete');
 		$ethnicity = Ethnicity::find($id);
 		if($ethnicity)
 		{
 			$ethnicity->delete();
 			Session::flash('success-msg', 'Ethnicity deleted successfully');
 			return Redirect::back();
 		}
 		else
 		{
 			Session::flash('error-msg', 'Ethnicity Id not found, Invalid Delete');
 			return Redirect::route('ethnicity-list');
 		}
 	}
}
