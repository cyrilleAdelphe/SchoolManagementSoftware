<?php

define('RESIZED_WIDTH',200);//the height is determined according to the original image's aspect ratio

define('TEMP_CREDENTIALS_DIRECTORY', app_path() . '/.credentials');
define('TEMP_CREDENTIALS_PATH', TEMP_CREDENTIALS_DIRECTORY . '/drive-api-quickstart.json');
define('TEMP_CLIENT_SECRET_PATH', TEMP_CREDENTIALS_DIRECTORY . '/client_secret.json');

class DownloadManagerController extends BaseController
{	
	protected $module_name = 'download-manager';
 	protected $model_name = 'DownloadManager';
 	protected $view = 'download-manager.views.';

 	public $current_user;
	public $role;

 	protected $google_drive;

 	//contains [number of folders,number of files] contained in a directory including it's sub-directory
 	//the index is the 'id' field in the database
 	//each element is an array, the index 'no_of_folders' is number of folders and 'no_of_files' the number of files
 	protected $google_drive_no_of_files = array();

	public function __construct()
	{
		parent::__construct();
		$this->google_drive = new EasyDriveAPI2(Request::url());
	}

	public function emptyNumberOfFilesBuffer()
	{
		/* helper function */
		$this->google_drive_no_of_files = array();
	}

	public function computeNumberOfFilesBuffer($download_manager_root = null)
	{
		/* helper function */
		$this->emptyNumberOfFilesBuffer();

		//recursively build the number of files/folder starting from root
		if($download_manager_root == null)
		{
			$download_manager_root = DownloadManager::select('id','google_file_id')
					->where('filename',DOWNLOAD_MANAGER_FOLDER)
					->where('parent_id',null)// in the root folder (no parent)
					->where('mime_type',EasyDriveAPI2::$folder_mime_type)
					->first();
		}
		
		
		$this->recursivelyFindNumberOfFilesBuffer($download_manager_root['id']);	
		Session::put('no_of_files_buffer',$this->google_drive_no_of_files);
	}

	public function generateBreadCrumbs($folder_id, $route='-backend-folder')
	{
		/* helper function */
		/* Following are for breadcrumb generation */
		$bread_crumbs = array();
		$iter_folder_id = $folder_id;
		do
		{
			$iter_folder = DownloadManager::select('id','filename','google_file_id','parent_id')
										->where('id',$iter_folder_id)
										->first();
			$bread_crumbs[$iter_folder['filename']] = URL::route($this->module_name. $route,[$iter_folder['id'],$iter_folder['google_file_id']]);
			
			$iter_folder_id = $iter_folder['parent_id'];//next iteration starts with the id of its parent
		}while($iter_folder_id);

		$bread_crumbs_string = array();
		foreach(array_reverse($bread_crumbs) as $folder_name => $url)
		{
			if($route!='-frontend-files' || $folder_name!='download manager')//don't want the root to show up when not backend
				$bread_crumbs_string[] = '<a href = "' . $url . '">' . $folder_name . '</a>';
		}
		return $bread_crumbs_string;
	}

	public function recursivelyFindNumberOfFilesBuffer($folder_id)
	{
		/* helper function */
		$folder_count = 0;
		$file_count = 0;

		$children_folders = DownloadManager::select('id')
								->where('parent_id',$folder_id)
								->where('mime_type',EasyDriveAPI2::$folder_mime_type)
								->get();
		foreach($children_folders as $child_folder)
		{
			$folder_count++;//each sub-category
			$this->recursivelyFindNumberOfFilesBuffer($child_folder['id']);
			$folder_count += $this->google_drive_no_of_files[$child_folder['id']]['no_of_folders'];//number of folders (recursive) within the sub-categories
			$file_count += $this->google_drive_no_of_files[$child_folder['id']]['no_of_files'];//number of files (recursive) within the sub-categories
		}

		//files within the category (folder)
		$file_count += DownloadManager::select('id')
								->where('parent_id',$folder_id)
								->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
								->count();

		$this->google_drive_no_of_files[$folder_id] = array('no_of_folders' => $folder_count,
															'no_of_files' => $file_count);

	}

	public function getFolderInfo($folder_id)
	{
		/* helper function */
		$parent = DownloadManager::select('id','filename','google_file_id','parent_id','description','no_of_downloads')
								->where('id',$folder_id)
								->first();

		return array('id'=>$parent['id'],
						'filename'=>$parent['filename'],
						'google_file_id'=>$parent['google_file_id'],
						'parent_id'=>$parent['parent_id'],
						'description'=>$parent['description']);		

	}

	public function getFolderFilesInfo($folder_id)
	{
		/* helper function */

		$children = DownloadManager::select('id','filename','google_file_id','mime_type','description','no_of_downloads','tags')
								->where('parent_id',$folder_id)
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->get();
		$children_file_array = array();

		foreach($children as $child)
		{
			$child_array = array(
									'id' => $child['id'],
									'filename' => $child['filename'],
									'google_file_id' => $child['google_file_id'],
									'mime_type' => $child['mime_type'],
									'description' => $child['description'],
									'tags' => $child['tags']
								);

			
			$child_array['no_of_downloads'] = $child['no_of_downloads'];
			$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
			$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			$children_file_array[] = $child_array;
		}

		return $children_file_array;
	}


	public function backendAccessLight()
	{
		$download_manager_root = DownloadManager::select('id','google_file_id')
						->where('filename',DOWNLOAD_MANAGER_FOLDER)
						->where('parent_id',null)// in the root folder (no parent)
						->where('mime_type',EasyDriveAPI2::$folder_mime_type);

		$no_of_download_managers = $download_manager_root->count();
		if ($no_of_download_managers == 0)
		{
			echo 'download manager does not exist';
			die();
		}
		elseif ($no_of_download_managers >1)
		{
			echo 'more than one download managers!!';
			die();
		}
		else
		{
			$folder = $download_manager_root->first();

			$this->computeNumberOfFilesBuffer($download_manager_root->first());

			return $this->backendSubCategories($folder['id'],$folder['google_file_id']);
		}

	}

	public function frontend()
	{
		return $this->frontendFilesAll();
		
		/*$download_manager_root = DownloadManager::select('id','google_file_id')
						->where('filename',DOWNLOAD_MANAGER_FOLDER)
						->where('parent_id',null)// in the root folder (no parent)
						->where('mime_type',EasyDriveAPI2::$folder_mime_type);
		$folder = $download_manager_root->first();
		return $this->frontendFiles($folder['id'],$folder['google_file_id']);*/

		//echo DownloadHelper::getCategoryTree();
		//return View::make('download-manager.views.frontend.main');
	}

	public function getDriveConfig() 
	{
		return View::make($this->view . 'drive-config')
			->with('role', $this->role)
			->with('current_user', $this->current_user);
	}

	public function postDriveConfig()
	{
		$client = new Google_Client();

		if (Input::hasFile('client_secret'))
		{
			Input::file('client_secret')->move(TEMP_CREDENTIALS_DIRECTORY, 'client_secret.json');
		}

		if (Input::has('application_name') && File::exists(TEMP_CLIENT_SECRET_PATH))
		{
			$client->setApplicationName(Input::get('application_name'));
		  $client->setScopes(Google_Service_Drive::DRIVE);
		  $client->setAuthConfigFile(TEMP_CLIENT_SECRET_PATH);
		  $client->setAccessType('offline');
		}
		else
		{
			return View::make($this->view . 'drive-config');
		}

		if (!Input::has('validation_code'))
		{
			$auth_url = $client->createAuthUrl();
			return View::make($this->view . 'drive-config')
				->with('auth_url', $client->createAuthUrl())
				->with('application_name', Input::get('application_name'));
		}
	  // Load previously authorized credentials from a file.
	  $credentials_path = TEMP_CREDENTIALS_PATH;

		$auth_code = trim(Input::get('validation_code'));

    // Exchange authorization code for an access token.
    try
    {
    	$access_token = $client->authenticate($auth_code);	
    }
    catch(Google_Auth_Exception $e)
    {
    	Session::flash('error-msg', $e->getMessage());
    	return Redirect::back();
    }

    // Store the credentials to disk.
    if(!file_exists(dirname($credentials_path))) 
    {
      mkdir(dirname($credentials_path), 0700, true);
    }
    File::put($credentials_path, $access_token);

    echo 'Drive successfully configured';
	}

	public function getConfig($folder_id, $google_file_id)
	{
		$config_file = app_path().'/modules/'.$this->module_name.'/config.json';
		$config = json_decode(
						File::get($config_file), 
						true
					);
		return View::make($this->view . 'config')
				->with('config',$config)
				->with('parent',$this->getFolderInfo($folder_id))
				->with('current_user', $this->current_user);
	}

	public function postConfig($folder_id, $google_file_id)
	{
		$validator = Validator::make(Input::all(),
			array(
				'max_show' => ['required','integer','min:1'],
			)
		);

		if($validator->fails())
		{
			//redirect with error message
			return Redirect::route('download-manager-config-get',[$folder_id, $google_file_id])
					->withErrors($validator)
					->withInput()
					->with('parent',$this->getFolderInfo($folder_id))
					->with('current_user', $this->current_user);
		}

		
		$config_file = app_path().'/modules/'.$this->module_name.'/config.json';
		$config = json_decode(
						File::get($config_file), 
						true
					);

		$config['max_show'] = Input::get('max_show');
		

		$result = HelperController::writeConfig($config,$config_file);

		if ($result['status'] == 'error')
		{
			Session::flash('error-msg','Error updating configuration');
		}
		else
		{
			Session::flash('success-msg','Configuration updated');
		}

		return Redirect::route('download-manager-config-get',[$folder_id, $google_file_id])
					->with('config',$config)
					->with('parent',$this->getFolderInfo($folder_id))
					->with('current_user', $this->current_user);
		
	}

	public function backendSubCategories($folder_id, $google_file_id)
	{
		$bread_crumbs_string = $this->generateBreadCrumbs($folder_id,'-backend-subcategories');

		$children = DownloadManager::select('id','filename','google_file_id','mime_type','description','no_of_downloads')
								->where('parent_id',$folder_id)
								->where('mime_type', EasyDriveAPI2::$folder_mime_type)
								->get();
		$children_folder_array = array();

		foreach($children as $child)
		{
			$child_array = array(
									'id' => $child['id'],
									'filename' => $child['filename'],
									'google_file_id' => $child['google_file_id'],
									'mime_type' => $child['mime_type'],
									'description' => $child['description']
								);

			
			$child_array['redirect'] = URL::route($this->module_name.'-backend-subcategories',['id'=>$child['id'],'google_file_id'=>$child['google_file_id']]);
			
			
			$sub_categories = DownloadManager::select('id')
													->where('parent_id',$child_array['id'])
													->where('mime_type',EasyDriveAPI2::$folder_mime_type)
													->count();

			$sub_files = DownloadManager::select('id')
													->where('parent_id',$child_array['id'])
													->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
													->count();


			//only if a folder is childless, do we offer delete link
			if($sub_categories==0 && $sub_files==0)
			{
				$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			}
			$children_folder_array[] = $child_array;
			
		}

		
		if(!Session::has('no_of_files_buffer'))
		{
			$this->computeNumberOfFilesBuffer();
		}

		$permissions = $this->getPermissions($route_names = array('can_view' => 'download-manager-backend-subcategories', 'can_delete' => 'download-manager-backend-file-remove', 'can_edit' => 'download-manager-backend-edit-get')) ;

		return View::make($this->view . 'subcategories')
				->with('breadcrumb',$bread_crumbs_string)
				->with('children_folder',$children_folder_array)
				->with('parent',$this->getFolderInfo($folder_id))
				->with('current_user', $this->current_user)
				->with('permissions', $permissions);
	}

	public function backendFiles($folder_id, $google_file_id)
	{
		$bread_crumbs_string = $this->generateBreadCrumbs($folder_id,'-backend-files');

		$children = DownloadManager::where('parent_id',$folder_id)
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->get();
		$children_file_array = array();

		foreach($children as $child)
		{
			$child_array = $child;
							// array(
							// 		'id' => $child['id'],
							// 		'filename' => $child['filename'],
							// 		'google_file_id' => $child['google_file_id'],
							// 		'mime_type' => $child['mime_type'],
							// 		'description' => $child['description'],
							// 		'tags' => $child['tags']
							// 	);

			
			$child_array['no_of_downloads'] = $child['no_of_downloads'];
			$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
			$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			$children_file_array[] = $child_array;
		}

		Session::put('children',$children_file_array);

		$model = $this->model_name;
		$model = new $model;

		$permissions = $this->getPermissions($route_names = array('can_download' => 'download-manager-backend-file-download', 'can_delete' => 'download-manager-backend-file-remove', 'can_edit' => 'download-manager-backend-upload-files')) ;

		return View::make($this->view . 'files')
				->with('breadcrumb',$bread_crumbs_string)
				->with('children_files',$children_file_array)
				->with('parent',$this->getFolderInfo($folder_id))
				->with('current_user', $this->current_user)
				->with('permissions', $permissions);

	}

	public function frontendFilesAll()
	{
		/*$children = DownloadManager::where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->paginate(10);
		
		$children_file_array = array();

		foreach($children as $child)
		{
			$child_array = $child;
			$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
			//$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			$children_file_array[] = $child_array;
		}


		$featured_children = DownloadManager::where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_featured','yes')
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->paginate(10);

		$featured_children_array = array();
		foreach($featured_children as $child)
		{
			$child_array = $child;
			$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
			//$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			$featured_children_array[] = $child_array;
		}
		*/
	
		$children_files = DownloadManager::where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->paginate(10);		

		$featured_files = DownloadManager::where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
									->where('is_featured','yes')
									->where('is_active','yes')
									->orderBy('created_at','DESC')
									->paginate(10);

		$most_downloaded = DownloadManager::where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('no_of_downloads','DESC')
								->paginate(10);		

		$model = $this->model_name;
		$model = new $model;

		//$permissions = $this->getPermissions($route_names = array('can_download' => 'download-manager-backend-file-download', 'can_delete' => 'download-manager-backend-file-remove', 'can_edit' => 'download-manager-backend-upload-files')) ;

		return View::make($this->view . 'frontend.generalfiles')
				->with('children_files',$children_files)
				->with('recent_files',$children_files)
				->with('featured_files',$featured_files)
				->with('most_downloaded',$most_downloaded)
				->with('current_user', $this->current_user)
				//->with('permissions', $permissions)
				->with('files_paginated',$children_files)
				->with('most_downloaded_paginated',$most_downloaded)
				->with('featured_paginated',$featured_files);

	}

	public function frontendFiles($folder_id, $google_file_id)
	{
		$bread_crumbs_string = $this->generateBreadCrumbs($folder_id,'-frontend-files');

		$children = DownloadManager::where('parent_id',$folder_id)
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->paginate(10);
		
		$children_file_array = array();

		foreach($children as $child)
		{
			$child_array = $child;
			$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
			//$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
			$children_file_array[] = $child_array;
		}

		
		// $featured_children = DownloadManager::where('parent_id',$folder_id)
		// 						->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
		// 						->where('is_featured','yes')
		// 						->where('is_active','yes')
		// 						->orderBy('created_at','DESC')
		// 						->paginate(10);

		// $featured_children_array = array();
		// foreach($featured_children as $child)
		// {
		// 	$child_array = $child;
		// 	$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
		// 	//$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
		// 	$featured_children_array[] = $child_array;
		// }

		$children_files = DownloadManager::where('parent_id',$folder_id)
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->paginate(10);		

		$featured_files = DownloadManager::where('parent_id',$folder_id)
									->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
									->where('is_featured','yes')
									->where('is_active','yes')
									->orderBy('created_at','DESC')
									->paginate(10);

		$most_downloaded = DownloadManager::where('parent_id',$folder_id)
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('no_of_downloads','DESC')
								->paginate(10);		


		Session::put('children',$children_file_array);
		Session::put('parent_folder',$this->getFolderInfo($folder_id));
		

		$model = $this->model_name;
		$model = new $model;

		//$permissions = $this->getPermissions($route_names = array('can_download' => 'download-manager-backend-file-download', 'can_delete' => 'download-manager-backend-file-remove', 'can_edit' => 'download-manager-backend-upload-files')) ;

		return View::make($this->view . 'frontend.main')
				->with('breadcrumb',$bread_crumbs_string)
				->with('children_files',$children_files)
				->with('recent_files',$children_files)
				->with('featured_files',$featured_files)
				->with('most_downloaded',$most_downloaded)
				->with('parent',$this->getFolderInfo($folder_id))
				->with('current_user', $this->current_user)
				//->with('permissions', $permissions)
				->with('files_paginated',$children)
				->with('featured_paginated',$featured_files);

	}

	public function frontendFileSearch()
	{
		$query = Input::get('q');
		$matching_filenames = DB::table('download_manager')
								->where('is_active','yes')
								->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
								->where('filename', 'like', '%'.$query.'%');

		$matching_tags = DB::table('download_manager')
								->where('is_active','yes')
								->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
								->where('tags', 'like', '%'.$query.'%');

		$matching_descriptions = DB::table('download_manager')
									->where('is_active','yes')
									->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
									->where('description', 'like', '%'.$query.'%');
		
		$query_files = $matching_filenames->union($matching_tags)->union($matching_descriptions)->get();

		return View::make('download-manager.views.frontend.search')
						->with('query_files',$query_files);

		
	}
	
	public function getFrontendFileUpload()
	{
		$user_folder = DownloadManager::where('filename','User Files')
										->where('mime_type',EasyDriveAPI2::$folder_mime_type)
										->first();
		if($user_folder)
		{
			$children = DownloadManager::where('parent_id',$user_folder['id'])
								->where('mime_type', '!=', EasyDriveAPI2::$folder_mime_type)
								->where('is_active','yes')
								->orderBy('created_at','DESC')
								->get();
		
			$children_file_array = array();

			foreach($children as $child)
			{
				$child_array = $child;
				$child_array['no_of_downloads'] = $child['no_of_downloads'];
				$child_array['download_link'] = URL::route($this->module_name . '-backend-file-download',[$child['id'],$child['google_file_id']]);//$this->google_drive->getDownloadLink($child['google_file_id']);
				//$child_array['delete_link'] = URL::route($this->module_name.'-backend-file-remove',[$child['id'],$child['google_file_id']]);
				$children_file_array[] = $child_array;
			}

			Session::put('children',$children_file_array);
			Session::put('parent_folder',$this->getFolderInfo($user_folder['id']));
			

			$model = $this->model_name;
			$model = new $model;

			$permissions = $this->getPermissions($route_names = array('can_download' => 'download-manager-backend-file-download', 'can_delete' => 'download-manager-backend-file-remove', 'can_edit' => 'download-manager-backend-upload-files')) ;

			return View::make('download-manager.views.frontend.fileupload');

		}
		else
		{
			echo 'User folder not created. Contact your site admin';
			die();
		}
	}

	public function backendAddCategory($folder_id,$google_file_id)
	{
		$children = DownloadManager::select('id','filename','google_file_id','mime_type','description','no_of_downloads')
								->where('parent_id',$folder_id)
								->where('mime_type',EasyDriveAPI2::$folder_mime_type)
								->get();

		
		$bread_crumbs_string = $this->generateBreadCrumbs($folder_id,'-backend-subcategories');
				
		/* Following are for viewing files /folders */
		$children_folder_array = array();
		foreach($children as $child)
		{
			
			$children_folder_array[] = array(
										'id' => $child['id'],
										'filename' => $child['filename'],
										'google_file_id' => $child['google_file_id'],
										'mime_type' => $child['mime_type'],
										'description' => $child['description']
									);
		}

		$parent_folder_array = $this->getFolderInfo($folder_id);
		//save the children to check if folder name already exists
		Session::put('children',$children_folder_array);
		Session::put('parent_folder',$parent_folder_array);

		
		return View::make($this->view . 'addcategory')
				->with('breadcrumb',$bread_crumbs_string)
				->with('parent',$parent_folder_array)
				->with('current_user', $this->current_user);
				
		
	}

	public function backendUploadFiles($folder_id,$google_file_id)
	{
		$children = DownloadManager::select('id','filename','google_file_id','mime_type','description','no_of_downloads')
								->where('parent_id',$folder_id)
								->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
								->get();

		
		$bread_crumbs_string = $this->generateBreadCrumbs($folder_id,'-backend-files');
				
		/* Following are for viewing files /folders */
		$children_file_array = array();
		foreach($children as $child)
		{
		
			$children_file_array[] = array(
										'id' => $child['id'],
										'filename' => $child['filename'],
										'google_file_id' => $child['google_file_id'],
										'mime_type' => $child['mime_type'],
										'description' => $child['description']
									);

		}

		$parent_folder_array = $this->getFolderInfo($folder_id);

		//save the children to check if folder name already exists
		Session::put('children',$children_file_array);
		Session::put('parent_folder',$parent_folder_array);

		
		return View::make($this->view . 'fileupload',[$parent_folder_array['id'],$parent_folder_array['google_file_id']])
				->with('breadcrumb',$bread_crumbs_string)
				->with('parent',$parent_folder_array)
				->with('current_user', $this->current_user);

	}

	
	public function getBackendEdit($id,$google_file_id)
	{
		$file = DownloadManager::find($id);
		if($file['google_file_id'] != $google_file_id)
		{
			echo 'invalid request';die();
		}

		$is_folder = ($file['mime_type'] == EasyDriveAPI2::$folder_mime_type);
		
		$categories = DownloadManager::where('mime_type',EasyDriveAPI2::$folder_mime_type)->get();
		
		
		foreach ($categories as $key => $category) {
			if($is_folder)
			{
				if(DownloadManager::where('parent_id',$category['id'])
										->where('mime_type','!=',EasyDriveAPI2::$folder_mime_type)
										->count()
					)
				{//take only the categories that don't have any files
					unset($categories[$key]);
				}
			}
			else
			{
				if(DownloadManager::where('parent_id',$category['id'])
										->where('mime_type',EasyDriveAPI2::$folder_mime_type)
										->count()
					)
				{//take only the categories that don't have subcategories themselves
					unset($categories[$key]);
				}
			}
				
		}
		

		$parent = DownloadManager::where('id',$file['parent_id'])->first();

		

		return View::make($this->view . 'edit')
						->with('file',$file)
						->with('name',$file['filename'])
						->with('description',$file['description'])
						->with('tags',$file['tags'])
						->with('id',$id)
						->with('google_file_id',$google_file_id)
						->with('mime_type',$file['mime_type'])
						->with('is_folder',$is_folder)
						->with('current_user', $this->current_user)
						->with('categories',$categories)
						->with('parent',$parent);

	}

	public function postBackendEdit()
	{
		//$this->google_drive->setRedirectUrl(Request::url());

		$input_data = Input::all();

		//verify that the new name doesn't clash with it's siblings 
		$sibling_names = array();

		$file = DownloadManager::find(Input::get('id'));
		$children = $this->getFolderFilesInfo($file['parent_id']);
		
				
		foreach($children as $sibling)
		{
			if($sibling['filename'] == Input::get('filename') && 
				$sibling['id'] != Input::get('id'))//the file being edited shows up as a sibling once
			{
				$sibling_names[] = $sibling['filename'];
			}

		}

		$validator = Validator::make($input_data,
											array('filename' => ['required','not_in:'.implode(',',$sibling_names)])
										);
		if($validator->fails())
		{
			return Redirect::route($this->module_name . '-backend-edit-get',[Input::get('id'),Input::get('google_file_id')])
					->withInput()
					->withErrors($validator);				
		}
		
		$file = DownloadManager::find(Input::get('id'));
		if(Input::get('filename') != $file['filename'])
		{
			if($this->google_drive->renameFile(Input::get('google_file_id'), Input::get('filename')))
			{
				
				$file->filename = Input::get('filename');
			}
			else
			{
				Session::flash('error-msg','Error renaming file');
			}	
		}

		if(Input::get('parent_id') != $file['parent_id'])
		{
			if($this->google_drive->moveFile(
												$file['google_file_id'],
												DownloadManager::where('id',Input::get('parent_id'))->first()['google_file_id']
											)
				)
			{
				$file->parent_id = Input::get('parent_id');
				Session::flash('success-msg','Item edited');
			}
			else
			{
				Session::flash('error-msg','Error moving file');
			}

		}
		
		$file->description = Input::get('description');
		$file->tags = Input::get('tags');
		$file->is_active = Input::get('is_active');
		$file->is_featured = Input::get('is_featured');
		$file->save();

		

		$this->computeNumberOfFilesBuffer();
		$parent = $this->getFolderInfo(Input::get('parent_id'));

		if(Input::get('mime_type') == EasyDriveAPI2::$folder_mime_type)
		{
			return Redirect::route($this->module_name . '-backend-subcategories',[$parent['id'],$parent['google_file_id']]);
		}
		else
		{
			
			return Redirect::route($this->module_name . '-backend-files',[$parent['id'],$parent['google_file_id']]);
		}

		
		//return Redirect::route($this->module_name . '-backend-folder',[$parent['id'],$parent['google_file_id']]);
						
	}

	public function postBackendFolderCreate()
	{
		if(!Session::has('children') || !Session::has('parent_folder'))
		{
			echo 'invalid request';
			die();
		}

		$this->google_drive->setRedirectUrl(Request::url());

		

		$parent = Session::get('parent_folder');

		//validate data, check if the folder name doesn't repeat
		$create_rule = (new DownloadManager)->createRule;
		$children_names = array_column(Session::get('children'), 'filename');
		$children_names = implode(',', $children_names);
		$create_rule['filename'][] = 'not_in:'.$children_names;

		
		$input_data = Input::all();
		$input_data['mime_type'] = EasyDriveAPI2::$folder_mime_type;
		$input_data['parent_id'] = $parent['id'];
		$input_data['google_file_id'] = 'to be filled later';//to be filled after folder creation (this is a lousy way!)

		
				
		$validator = Validator::make($input_data,$create_rule);
 		
 		if($validator->fails())
 		{
 			return Redirect::route($this->module_name.'-backend-add-category',[$parent['id'],$parent['google_file_id']])
 								->withErrors($validator)
 								->withInput();
 								

 			// return Redirect::route($this->module_name.'-backend-folder',[$parent['id'],$parent['google_file_id']])
 			// 			->withErrors($validator)
 			// 			->withInput()
 			// 			->with('parent',$parent);
 		}

 		$folder = $this->google_drive->createFolder($input_data['filename'],$parent['google_file_id']);
 		if($folder==null)
 		{
 			Session::flash('error-msg', 'Upload Error! Please try again later');
			//echo 'Failure';
			return Redirect::route($this->module_name.'-backend-subcategories',$parent);
			//return Redirect::route($this->module_name.'-backend-folder',$parent);
 		}
 		$input_data['google_file_id'] = $folder->getId();

 		$database_result = BaseModel::cleanDatabaseOperation([
 											[$this,'storeInDatabase',[$input_data]]
 										]);

 		if($database_result['success'])
		{
			Session::flash('success-msg', 'Sub-category created');
			//echo 'success';
			$this->computeNumberOfFilesBuffer();

			//return Redirect::route($this->module_name.'-backend-folder',$parent);
		}
		else
		{
			Session::flash('error-msg', 'Database Error! Please try again later'.$database_result['msg']);
			//echo 'Failure';
			//return Redirect::route($this->module_name.'-backend-folder',$parent);
		}

		return Redirect::route($this->module_name.'-backend-subcategories',[$parent['id'],$parent['google_file_id']]);
	}

	public function postBackendFileUpload()
	{
		if(!Session::has('children') || !Session::has('parent_folder'))
		{
			echo 'invalid request';
			die();
		}

		//$this->google_drive->setRedirectUrl(Request::url());

		// echo '<img src="https://drive.google.com/uc?id=0B74M3rx96gl9R0ZERDBVS19ram8">';
		// die();

		$parent = Session::get('parent_folder');//$parent = unserialize(Input::get('parent'));
		
		$input_data = Input::all();
		$input_data['parent_id'] = $parent['id'];
		$input_data['no_of_downloads'] = 0;

		// if (!Input::hasFile('fileToUpload'))
		// {
			
		// 	Session::flash('error-msg','Upload file not selected');

		// 	return Redirect::route($this->module_name.'-backend-upload-files',[$parent['id'],$parent['google_file_id']])
 	// 					->withInput()
 	// 					->with('parent',$parent);

			
		// }
		

		//validate data, check if the folder name doesn't repeat
		$create_rule = (new DownloadManager)->createRule;
		$children_names = array_column(Session::get('children'), 'filename');
		$children_names = implode(',', $children_names);
		$create_rule['filename'][] = 'not_in:'.$children_names;
		$create_rule['fileToUpload'] = ['required','max:20480'];
	

		if(Input::hasFile('fileToUpload'))	
		{
			$input_data['mime_type'] = Input::file('fileToUpload')->getMimeType();
		
			$input_data['google_file_id'] = 'to be filled later';//to be filled after file creation (this is a lousy way!)
			if($input_data['filename'] == '')
			{
				$input_data['filename'] = Input::file('fileToUpload')->getClientOriginalName();	
				Input::merge(array('filename' => $input_data['filename']));
			}
		}
		
		
		$validator = Validator::make($input_data,$create_rule);
 		
 		if($validator->fails())
 		{
 			if(Input::has('is_frontend') && Input::get('is_frontend')=='yes')
 			{
 				Session::flash('error-msg','Form error');
 				Session::flash('init_tab','upload');
 				return Redirect::route($this->module_name.'-frontend-files',[$parent['id'],$parent['google_file_id']])
 						->withInput()
 						->withErrors($validator);
 			}
 			else
 				return Redirect::route($this->module_name.'-backend-upload-files',[$parent['id'],$parent['google_file_id']])
 						->withInput()
 						->withErrors($validator);
 						

 			// return Redirect::route($this->module_name.'-backend-file-upload-get',[$parent['id'],$parent['google_file_id']])
 			// 			->withErrors($validator)
 			// 			->withInput();
 		}

 		$mime_exploded = explode('/', $input_data['mime_type']);
 		$file = Input::file('fileToUpload');
 		if($mime_exploded[0]=='image')
 		{
 			// configure with favored image driver (gd by default)
 			
 			$temp_file = base_path().'/foo.jpg';
			//Image::configure(array('driver' => 'imagick'));
			$file = Image::make($file);

			$original_width = $file->width();
			$original_height = $file->height();

			$resized_height = RESIZED_WIDTH * $original_height / $original_width;
 			$file->resize(RESIZED_WIDTH,$resized_height);

 			$file->save($temp_file);
 			$file = $temp_file;
 			
 		}

 		$file = $this->google_drive->insertFile($input_data['filename'],
 													'file created by google drive api',
 													$parent['google_file_id'],
 													$input_data['mime_type'],
 													$file
 												);
 		
 		$input_data['google_file_id'] = $file->getId();

 		$database_result = BaseModel::cleanDatabaseOperation([
 											[$this,'storeInDatabase',[$input_data]]
 										]);

 		if($database_result['success'])
		{
			Session::flash('success-msg', 'File created');
			//echo 'success';

			$this->computeNumberOfFilesBuffer();

			//return Redirect::route($this->module_name.'-backend-folder',$parent);
		}
		else
		{
			Session::flash('error-msg', 'Error! Please try again later');
			//echo 'Failure';
			//return Redirect::route($this->module_name.'-backend-folder',$parent);
		}

		if(Input::has('is_frontend') && Input::get('is_frontend')=='yes')
			return Redirect::route($this->module_name.'-frontend-file-upload-get');
		else
			return Redirect::route($this->module_name.'-backend-files',[$parent['id'],$parent['google_file_id']]);
 							
	}

	public function postBackendFilesUpload()
	{
		if(!Session::has('children') || !Session::has('parent_folder'))
		{
			echo 'invalid request';
			die();
		}

		$this->google_drive->setRedirectUrl(Request::url());

		// echo '<img src="https://drive.google.com/uc?id=0B74M3rx96gl9R0ZERDBVS19ram8">';
		// die();

				
		$parent = Session::get('parent_folder');//$parent = unserialize(Input::get('parent'));
		
		$input_data = Input::all();
		$input_data['parent_id'] = $parent['id'];
		$input_data['no_of_downloads'] = 0;

		//if(Input::hasFile('filesToUpload'))
		//{

		
		$files_to_upload = Input::file('filesToUpload');

		if (!Input::hasFile('filesToUpload'))
		{
			Session::flash('error-msg','Upload file(s) not selected');


			return Redirect::route($this->module_name.'-backend-upload-files',[$parent['id'],$parent['google_file_id']])
 						->withInput();
 						//->withErrors();
			
			// return Redirect::route($this->module_name.'-backend-files-upload-get',[$parent['id'],$parent['google_file_id']])
 			// 				->withInput();
		}
		

		//validate data, check if the folder name doesn't repeat
		$create_rule = (new DownloadManager)->createRule;
		$children_names = array_column(Session::get('children'), 'filename');
		$children_names = implode(',', $children_names);
		$create_rule['filename'][] = 'not_in:'.$children_names;
		$create_rule['fileToUpload'] = ['required','max:20480'];
	

		$error_msg = '';
		foreach($files_to_upload as $file_to_upload)
		{
			$input_data['mime_type'] = $file_to_upload->getMimeType();//EasyDriveAPI2::$folder_mime_type;
			$input_data['google_file_id'] = 'to be filled later';//to be filled after file creation (this is a lousy way!)
			$input_data['fileToUpload'] = $file_to_upload;

			$input_data['filename'] = $file_to_upload->getClientOriginalName();

			$validator = Validator::make($input_data,$create_rule);
	 		
	 		if($validator->fails())
	 		{
	 			$error = $validator->messages();

	 			
	 			$error_msg .= 'Error uploading '.$input_data['filename'].': ';
	 			$error_msg .= $error->has('filename') ? 'Filename already exists,' : '';
	 			$error_msg .= $error->has('fileToUpload') ? $error->first('fileToUpload') : '';
	 			$error_msg .= $error->has('meme_type') ? $error->first('meme_type') : '';
	 			$error_msg = trim($error_msg, ","); //trim any trailing or leading commas
	 			$error_msg .= '<br/>';
	 			continue;
	 		}

	 		$mime_exploded = explode('/', $input_data['mime_type']);
	 		
	 		if($mime_exploded[0]=='image')
	 		{
	 			// configure with favored image driver (gd by default)
	 			
	 			$temp_file = base_path().'/foo.jpg';
				//Image::configure(array('driver' => 'imagick'));
				$file_to_upload = Image::make($file_to_upload);

				$original_width = $file_to_upload->width();
				$original_height = $file_to_upload->height();

				$resized_height = RESIZED_WIDTH * $original_height / $original_width;
	 			$file_to_upload->resize(RESIZED_WIDTH,$resized_height);

	 			$file_to_upload->save($temp_file);
	 			$file_to_upload = $temp_file;
	 			
	 		}

	 		$google_file = $this->google_drive->insertFile($input_data['filename'],
	 													'file created by google drive api',
	 													$parent['google_file_id'],
	 													$input_data['mime_type'],
	 													$file_to_upload
	 												);
	 		
	 		$input_data['google_file_id'] = $google_file->getId();

	 		$database_result = BaseModel::cleanDatabaseOperation([
	 											[$this,'storeInDatabase',[$input_data]]
	 										]);
	 		if (!$database_result['success'])
	 		{
	 			$error_msg .= 'Error uploading '.$input_data['filename'].'<br/>';
	 		}


		}

		$this->computeNumberOfFilesBuffer();

		if($error_msg == '')
		{
			Session::flash('success-msg', 'File(s) created');
		}
		else
		{
			Session::flash('error-msg', $error_msg);
			
			/*
			echo $error_msg;
			echo '<br/>';
			$redirect = URL::route($this->module_name.'-backend-folder',Session::get('parent_folder'));
			echo '<a href = "';
			echo $redirect . '">';
			echo 'Back';
			echo '</a>';
			die();
			*/
		}

		
		return Redirect::route($this->module_name.'-backend-files',[$parent['id'],$parent['google_file_id']]);
		//return Redirect::route($this->module_name.'-backend-folder',$parent);
 		
	}

	public function backendFileRemove($file_id,$google_file_id)
	{

		$this->google_drive->setRedirectUrl(Request::url());

		// if(!Session::has('children') || !Session::has('parent_folder'))
		// {
		// 	echo 'invalid request';
		// 	die();
		// }

		$file = DownloadManager::select('id','google_file_id','filename','mime_type','parent_id')
								->where('id',$file_id)
								->first();

		
		$parent = $this->getFolderInfo($file['parent_id']);
		
		if ($file['mime_type'] == EasyDriveAPI2::$folder_mime_type)
		{
			
			$children = DownloadManager::where('parent_id',$file['id'])->get();
			if(!$children->isEmpty())
			{
				Session::flash('error-msg','Error! Category not empty');
				return Redirect::route($this->module_name.'-backend-subcategories',[$file_id, $google_file_id]);

				//return Redirect::route($this->module_name.'-backend-folder',Session::get('parent_folder'));

				/*echo 'Folder not empty';
				echo '<br/>';
				$redirect = URL::route($this->module_name.'-backend-folder',Session::get('parent_folder'));
				echo '<a href = "';
				echo $redirect . '">';
				echo 'Back';
				echo '</a>';
				die();
				*/
			}
		}

		if($this->google_drive->trashFile($google_file_id))
		{
			DownloadManager::destroy($file_id);
		}
		else
		{
			Session::flash('error-msg','Error deleting the item');
		}

		$this->computeNumberOfFilesBuffer();

		if ($file['mime_type'] == EasyDriveAPI2::$folder_mime_type)
		{
			return Redirect::route($this->module_name.'-backend-subcategories',[$parent['id'],$parent['google_file_id']]);
		}
		else
		{
			return Redirect::route($this->module_name.'-backend-files',[$parent['id'],$parent['google_file_id']]);
		}

		//return Redirect::route($this->module_name.'-backend-folder',Session::get('parent_folder'));

	}

	public function backendFileDownload($file_id,$google_file_id)
	{
		$file_model = DownloadManager::find($file_id);
		$file_model->no_of_downloads++;
		$file_model->save();
		//echo $this->google_drive->getDownloadLink($google_file_id);
		//die();
		header('location:'.$this->google_drive->getDownloadLink($google_file_id));
		exit();

	}

	private function getPermissions($route_names = array())
	{
		$model = $this->model_name;
		$model = new $model;
		$return = array();

		foreach($route_names as $permission_type => $route_name)
		{
			$return[$permission_type] = $model->checkPermissions($this->current_user, $this->module_name, $route_name);
		}

		return $return;
	}



}
?>