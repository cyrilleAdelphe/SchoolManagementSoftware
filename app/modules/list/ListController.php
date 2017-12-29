<?php
define('ICON_PATH',app_path().'/modules/list/asset/images');
class ListController extends BaseController
{
	protected $module_name = 'list';
 	protected $model_name = 'ListModel';
 	protected $view = 'list.views.';

 	public function getList()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_view'); 				 			
 		return View::make($this->view . 'list')
 				->with('list',ListModel::all());
 	}

 	public function postList()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_create'); 
 		$input_data = Input::all();

 		$create_rule = (new $this->model_name)->createRule;
		$create_rule['image'] = ['required','image','max:5000'];

				
		if(Input::hasFile('image'))
		{
			$file_controller = new FileUploadController;
			do 
			{
				$filenameToStore = $file_controller->filenameRandomizer('',true) . '.' . Input::file('image')->getClientOriginalExtension();
			}while($file_controller->checkIfFileExists(ICON_PATH, $filenameToStore));
			
			$input_data['icon'] = $filenameToStore;

			//$input_data['icon'] = uniqid() . '.' . Input::file('image')->getClientOriginalExtension();
			
		}

		$validator = Validator::make($input_data,$create_rule);

				 		
 		if($validator->fails())
 		{

 			return Redirect::route($this->module_name.'-create-get')
 						->withInput()
 						->withErrors($validator)
 						->with('list',ListModel::all());
 		}

 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$this,'storeInDatabase',[$input_data]]
 															]);
 		if($database_result['success'])
 		{
 			Session::put('success-msg','List created');
 			$filename =  ICON_PATH .'/'. $input_data['icon'];
 			Image::make($input_data['image'])->save($filename);
 		}
 		else
 		{
 			echo $database_result['msg'];die();
 			Session::put('error-msg','Error!!');
 		}

 		return Redirect::route($this->module_name.'-create-get')
 							->with('list',ListModel::all());
 		
 	}

 	public function getEdit($id,$title)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit'); 
 		return View::make($this->view . 'edit')
 						->with('item',ListModel::find($id));

 	}

 	public function postEdit()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit'); 
 		$input_data = Input::all();
 		$item = ListModel::find($input_data['id']);
 		$input_data['icon'] = $item['icon'];

 		$validator = $this->validateInput($input_data,true);
 		if($validator['status'] == 'error')
 		{
 			echo $validator['data']->getMessageBag();die();
 			return Redirect::route($this->module_name . '-edit-get',([$item['id'],$item['title']]))
 							->withInput()
 							->withErrors($validator['data'])
 							->with('item',$item);

 		}

 		

 		$item['title'] = $input_data['title'];
 		$item['information'] = $input_data['information'];
 		$item['order_index'] = $input_data['order_index'];
 		$item['is_active'] = $input_data['is_active'];

 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$item,'save',null]
 															]);

 		if($database_result['success'])
 		{
 			Session::put('success-msg','Item edited');
 			if(Input::hasFile('image'))
	 		{
	 			Image::make($input_data['image'])->save(ICON_PATH .'/'. $item['icon']);
	 		}
 		}
 		else
 		{
 			Session::put('error-msg','error editing item!!!');	
 		}

 		return Redirect::route($this->module_name.'-create-get')
 							->with('list',ListModel::all());

 		
 	}

 	public function getDelete($id,$title)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_delete'); 
 		$item = ListModel::find($id);
 		$file = $item['icon'];
 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$item,'delete',null]
 															]);

 		
 		if($database_result['success'])
 		{
 			Session::put('success-msg','Block deleted');
 			File::delete(ICON_PATH .'/'. $file);
 		}
 		else
 		{
 			Session::put('error-msg','Error deleting block!!');
 		}

 		return Redirect::route($this->module_name.'-create-get')
 							->with('list',ListModel::all());

 	}
}