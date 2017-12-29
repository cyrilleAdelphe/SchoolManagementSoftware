<?php

class TestimonialController extends BaseController
{
	protected $view = 'testimonial.views.';

	protected $model_name = 'Testimonial';

	protected $module_name = 'testimonial';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'content',
										'alias'			=> 'Content'
									),
									array
									(
										'column_name' 	=> 'show_in_module',
										'alias'			=> 'Show In Module'
									),
									array
									(
										'column_name' 	=> 'sort_order',
										'alias'			=> 'Order'
									)
								 );
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////// These are ajax functions /////////////////////////////////////////////////////////////////////////////////////////////////
	public function postCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
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
			$id = $this->storeInDatabase($data);	

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;
			//$file->getPathName();
			if(Input::hasFile('profile_pic'))
			{
				Queue::later((time() + 60), 'TestimonialQueue@makeThumbnail', array('file' => Input::file('profile_pic')->getPathName(), 'imaze_size' => getimagesize(Input::file('profile_pic')->getPathName()), 'id' => $id));
			}
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function postEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
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
			$id = $this->updateInDatabase($data);	

			$success = true;
			$msg = 'Record successfully updated';
			$param['id'] = $id;

			if(Input::hasFile('profile_pic'))
			{
				Queue::later((time() + 60), 'TestimonialQueue@makeThumbnail', array('file' => Input::file('profile_pic')->getPathName(), 'imaze_size' => getimagesize(Input::file('profile_pic')->getPathName()), 'id' => $id));
			}
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
			$param['id'] = $data['id'];
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}
	
}
