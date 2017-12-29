<?php


class HouseController extends BaseController
{
	protected $module_name = 'houses';
 	protected $model_name = 'House';
 	protected $view = 'house.views.';

 	public $role;

	public $current_user;

	public function getHouseView() {

		
		AccessController::allowedOrNot($this->module_name, 'can_view');
		$house = House::orderBy('id','DESC')->get();

		return View::make('houses.views.list-house')
					->with('house', $house);
	}

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$house = DB::table('houses')->select('house_name','id')->where('is_active', 'yes')->lists('house_name' , 'id');

		
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('house', $house);
					
	}


	public function getEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');

		
		$house = DB::table('houses')->select('house_name','id')->where('is_active', 'yes')->lists('house_name' , 'id');	
	
		return View::make($this->view.'edit')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('house', $house);
					
					
	}

	public function getViewView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_view');
		

		$house_name = DB::table('houses')
						->join('student_registration','student_registration.house_id','=','houses.id')
						->where('student_registration.id',$id)
						->pluck('houses.house_name');

		

		return View::make($this->view.'view')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('house_name', $house_name);

	}


	public function postCreatehouse() 
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$model = new House;

		$validator = Validator::make(Input::all(),$model->rules);

		if($validator->fails())
		{
			return Redirect::back()
							->withInput()
							->withErrors($validator);
		}



		$house = new House;
		$house->house_name = Input::get('house_name');
		$house->house_code = Input::get('house_code');
		$house->is_active  = Input::get('is_active');
		$house->save();

		Session::flash('success-msg','House Created successfully');
		return Redirect::back();
	}

	public function getEditHouse($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$house = House::find($id);
		return View::make('houses.views.edit-house')
					->with('house', $house);

	}

	public function postUpdateHouse($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
/*		$model = new House;

		$validator = Validator::make(Input::all(),$model->rules);

		if($validator->fails())
		{
			return Redirect::back()
							->withInput()
							->withErrors($validator);
		}*/

		$house = House::find($id);
		$house->house_name = Input::get('house_name');
		$house->house_code = Input::get('house_code');
		$house->is_active  = Input::get('is_active');
		$house->save();

		Session::flash('success-msg','House Updated successfully');
		return Redirect::route('list-house');
	}

	public function getDeleteHouse($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_delete');
		$house = House::find($id);
		$house->delete();
		Session::flash('success-msg', 'House Deleted successfully');
		return Redirect::back();
	}

}