<?php
define('ICON_PATH',app_path().'/modules/blocks/asset/images');
class BlocksController extends BaseController
{
	protected $module_name = 'blocks';
 	protected $model_name = 'BlocksModel';
 	protected $view = 'blocks.views.';

 	public function getCreate()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_create'); 				 			
 		return View::make($this->view . 'create')
 				->with('blocks',BlocksModel::all());
 	}

 	public function postCreate()
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
 						->with('block',BlocksModel::all());
 		}

 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$this,'storeInDatabase',[$input_data]]
 															]);
 		if($database_result['success'])
 		{
 			Session::put('success-msg','Block created');
 			$filename =  ICON_PATH .'/'. $input_data['icon'];
 			Image::make($input_data['image'])->save($filename);
 		}
 		else
 		{
 			echo $database_result['msg'];die();
 			Session::put('error-msg','Error!!');
 		}

 		return Redirect::route($this->module_name.'-create-get')
 							->with('blocks',BlocksModel::all());
 		
 	}

 	public function getEdit($id,$title)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		return View::make($this->view . 'edit')
 						->with('item',BlocksModel::find($id));

 	}

 	public function postEdit()
 	{	
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		$input_data = Input::all();
 		$item = BlocksModel::find($input_data['id']);
 		$input_data['icon'] = $item['icon'];

 		$validator = $this->validateInput($input_data,true);
 		if($validator['status'] == 'error')
 		{
 			return Redirect::route($this->module_name . '-edit-get',([$item['id'],$item['title']]))
 							->withInput()
 							->withErrors($validator['data'])
 							->with('item',$item);

 		}

 		

 		$item['title'] = $input_data['title'];
 		$item['information'] = $input_data['information'];
 		$item['order_index'] = $input_data['order_index'];
 		$item['class'] = $input_data['class'];
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
 							->with('blocks',BlocksModel::all());

 		
 	}

 	public function getDelete($id,$title)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_delete');
 		$item = BlocksModel::find($id);
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
 							->with('blocks',BlocksModel::all());

 	}
}