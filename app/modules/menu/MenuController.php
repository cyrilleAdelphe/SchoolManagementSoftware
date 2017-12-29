<?php
class MenuController extends BaseController
{
	protected $module_name = 'menu';
 	protected $model_name = 'Menus';
 	protected $view = 'menu.views.';

 	// public function show()
 	// {
 	// 	return View::make($this->view . 'index')
 	// 					->with('list',ListModel::orderBy('order_index')->get())
 	// 					->with('blocks',BlocksModel::orderBy('order_index')->get());
 	// }

 	public function getCreate()
 	{		
 		AccessController::allowedOrNot($this->module_name, 'can_create'); 				 			
 		return View::make($this->view . 'menucreate')
 						->with('articles',Articles::all())
 						->with('menus',Menus::all());
 	}

 	public function postCreate()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_create');
 		$articles = Articles::all();
 		$menus = Menus::all();

 		$input_data = Input::all();
 		$validator = $this->validateInput($input_data);

 		if(!Input::hasFile('profile_pic') && Input::get('article_id'))
 		{
 			if(!$validator['data'])
 			{
 				$validator['data'] = new Illuminate\Support\MessageBag;
 			}
 			$validator['status'] = 'error';
 			$validator['data']->getMessageBag()->add('profile_pic','Image required for article menu');
 			
 		}
 		
 		
 		if($validator['status'] == 'error')
 		{
 			Session::flash('error-msg', 'There are some validation errors!');
 			return Redirect::route($this->module_name . '-create-get')
 					->withInput()
 					->withErrors($validator['data'])
 					->with('articles',$articles)
 					->with('menus',$menus);
 		}
 		

 		if($input_data['parent_id']==0)
 		{
 			$input_data['parent_id'] = null;	
 		}

 		if($input_data['article_id']==0)
 		{
 			$input_data['article_id'] = null;	
 		}

 		if(empty($input_data['external_link']))
 		{
 			$input_data['external_link'] = null;	
 		}



 		
 		$database_result = Articles::cleanDatabaseOperation([
																[$this,'storeInDatabase',[$input_data]]
															]);
 		

 		if($database_result['success'])
 		{
 			Session::flash('success-msg','Menu created');
 			if(Input::hasFile('profile_pic'))
 			{
 				Image::make($input_data['profile_pic'])->save(app_path() . '/modules/menu/asset/images/' . $database_result['param']['id'] . '.jpg');
 				
 			}
 		}
 		else
 		{
 			Session::flash('error-msg','Error in creating menu');
 		}

 		return Redirect::route($this->module_name . '-create-get')
 					->with('articles',$articles)
 					->with('menus',$menus);



 	}

 	public function getEdit($id,$alias)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		$menu = Menus::find($id);

 		if($menu['alias']!=$alias)
 		{
 			echo 'Invalid Request';
 			die();
 		}


 		return View::make($this->view . 'menuedit')
 				->with('menu',$menu)
 				->with('articles',Articles::all());

 	}

 	public function postEdit()
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_edit');
 		$input_data = Input::all();

 		$update_rules = (new Menus)->updateRule;
 		//if the rule is unique, field value of the current record
 		foreach($update_rules as $field => $rules)
 		{
 			foreach($rules as $key=>$rule)
 			{
	 			if(substr($rule, 0,strlen('unique')) == 'unique')
	 			{
	 				$rule .= ',' . Input::get('id');
	 				$update_rules[$field][$key] = $rule;
	 			}
 			}
 		}

 		$validator = Validator::make($input_data,$update_rules);

 		if($validator->fails())
 		{
 			return Redirect::route($this->module_name . '-edit-get',[$input_data['id'],
 									Menus::select('alias')->where('id',$input_data['id'])->first()['alias']]
 								)
	 							->withInput()
	 							->withErrors($validator);
 		}

 		$menu = Menus::find($input_data['id']);
 		$menu['title'] = $input_data['title'];
 		$menu['alias'] = $input_data['alias'];
 		$menu['article_id'] = $input_data['article_id']==0 ? null : $input_data['article_id'];
 		$menu['parent_id'] = $input_data['parent_id']==0 ? null : $input_data['parent_id'];
 		$menu['is_active'] = $input_data['is_active'];
 		$menu['order_index'] = $input_data['order_index'];
 		$menu['external_link'] = $input_data['external_link']==''? null : $input_data['external_link'];

 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$menu,'save',null]
 															]);

 		if($database_result['success'])
 		{
 			Session::flash('success-msg', 'Menu edited');
 			if(Input::hasFile('profile_pic') && $input_data['article_id'])
 			{
 				Image::make($input_data['profile_pic'])->save(app_path() . '/modules/menu/asset/images/' . $input_data['id'] . '.jpg');
 			}
 		}
 		else
 		{
 			Session::flash('error-msg','Error!!');
 		}

 		return Redirect::route($this->module_name . '-create-get')
 					->with('articles',Articles::all())
 					->with('menus',Menus::all());
 		
 	}

 	public function getDelete($id,$alias)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_delete');
 		$menu = Menus::find($id);

 		if($menu['alias']!=$alias)
 		{
 			echo 'Invalid Request';
 			die();
 		}

 		if(Menus::where('parent_id',$id)->exists())
 		{
 			echo 'menu not empty!';
 			die();
 		}

 		
 		$database_result = BaseModel::cleanDatabaseOperation([
 																[$menu,'delete',null]
 															]);
 		if($database_result['success'])
 		{
 			Session::flash('success-msg','Menu Deleted');
 		}
 		else
 		{
 			Session::flash('error-msg','Error!!');
 		}

 		return Redirect::route($this->module_name . '-create-get')
 					->with('articles',Articles::all())
 					->with('menus',Menus::all());

 	}

 	public function getViewview($id)
 	{
 		AccessController::allowedOrNot($this->module_name, 'can_view');
 		$menu = Menus::find($id);
 		return View::make('menu.views.menuview')
 					->with('menu',$menu);

 	}

 	
		
}