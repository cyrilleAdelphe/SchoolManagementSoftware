<?php


class RemarkSettingController extends BaseController
{
	protected $view = 'cas.views.remark-setting.';

	protected $model_name = 'RemarkSetting';

	protected $module_name = 'remark-setting';

	protected $role;
public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'remarks_number',
										'alias'			=> 'remarks_number'
									),
									array
									(
										'column_name' 	=> 'remarks',
										'alias'			=> 'remarks'
									),
								);


  

	public function remarkSettingList()
	{
		AccessController::allowedOrNot('remark-setting', 'can_view');
		$data = (new RemarkSetting)->getListViewData();
		return View::make($this->view.'list')
					->with('role', $this->role)
          ->with('data', $data);

	}

	public function postCreateView() 
  {
    AccessController::allowedOrNot($this->module_name, 'can_create');
    $success = false;
    $msg = '';
    $param = array('id' => 0);
    $data = Input::all();
    


    foreach($data['remarks_number'] as $index => $val)
    {
    
      $d = ['remarks_number' => $val, 'remarks' => $data['remarks'][$index]];
        
      $result = $this->validateInput($d);
      
      if($result['status'] == 'error')
      {
        Session::flash('error-msg', ConfigurationController::translate('The Number Already Exists'));


        return Redirect::route($this->module_name.'-list')
              ->withInput()
              ->with('errors', $result['data']);
      }
    }

    try
    {   $insert_data = array();
        $i = 0;
        $j = 0;
                     
              $data['remarks_number'] = Input::get('remarks_number');
              $data['remarks'] = Input::get('remarks');
                  // echo '<pre>';
                  // print_r($data);
                  // die();       
              foreach ($data['remarks_number'] as $key => $value) {
                  
                  $insert_data[$i]['remarks_number'] = $value;
                  $i++;
              }
              
              foreach ($data['remarks'] as $key => $value) {
                  $insert_data[$j]['remarks'] = $value;
                  $j++;   
              }
              // echo '<pre>';
              // print_r($insert_data);
              // die();
              foreach ($insert_data as $key) {
                      RemarkSetting::create($key);
                      }
              return Redirect::route('remark-setting-list');
            
      $success = true;
      $msg = 'Record successfully created';
      $param['id'] = $id;
    }
    catch(PDOException $e)
    {
      $success = false;
      $msg = $e->getMessage();
    }
    
    return $this->redirectAction($success, 'create', $param, $msg);
}

            
    

              // for editing remark

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
      Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
      return Redirect::route($this->module_name.'-edit-get', array($id))
            ->withInput()
            ->with('errors', $result['data']);
    }
    
    try
    {
      $id = $this->updateInDatabase($data); 

      $success = true;
      $msg = 'Record successfully updated';
      $param['id'] = $id; 
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