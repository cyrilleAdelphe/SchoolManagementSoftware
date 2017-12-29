<?php

class ContactUsController extends BaseController
{
	protected $view = 'contact-us.views.';

	protected $model_name = 'ContactUs';

	protected $module_name = 'contact-us';

	protected $role;

	public $current_user;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'sender_email',
										'alias'			=> 'Sender Email'
									),

									array
									(
										'column_name' 	=> 'sender_location',
										'alias'			=> 'Sender Location'
									),

									array
									(
										'column_name' 	=> 'subject',
										'alias'			=> 'Subject'
									),

									array
									(
										'column_name'	=> 'query',
										'alias'			=> 'Query'
									)
								 );

	public function postCreateView()
	{

		AccessController::allowedOrNot($this->module_name, 'can_create');
		$success = false;
		$msg = '';
		$param = array('id' => 0);
		$data = Input::all();
		/*$data = [  'sender_email'		=> 'roshan@email.com',
							'sender_location'	=> 'test',
							'subject'			=> 'test',
							'query'				=> 'test',
							'is_active'			=> 'yes'
						  ];*/

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
			Mail::send('contact-us.views.mail', 
						array('query'=>Input::get('query'),
							  'sender_location'=>Input::get('sender_location'),
							  'sender_email'=>Input::get('sender_email'),
							  'sender_name'=>Input::get('sender_name'),						 
							  ), 
						
						function($message){
        					$message->to((new ContactUsHelper)->getConfig()['recipient_email'])
        							->subject(Input::get('subject'));
    });
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return Redirect::route('home-frontend');
	}

	public function postConfig()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create,can_edit');
		$validator = Validator::make(Input::all(),
			array(
				'recipient_email' => ['required','email']
			)
		);

		if($validator->fails())
		{
			//redirect with error message
			return Redirect::route('contact-us-config-get')
					->withErrors($validator)
					->withInput();
		}

		$helper = new ContactUsHelper;
		$config = $helper->getConfig();

		$config['recipient_email'] = Input::get('recipient_email');
		
		$result = $helper->writeConfig($config);

		if ($result['status'] == 'error')
		{
			Session::flash('error-msg','Error updating configuration');
		}
		else
		{
			Session::flash('success-msg','Configuration updated');
		}
		return Redirect::route('contact-us-config-get')
					->with('config',(new ContactUsHelper)->getConfig());
	}

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		return View::make($this->view.'create')
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('actionButtons', $this->getActionButtons());
	}
}
