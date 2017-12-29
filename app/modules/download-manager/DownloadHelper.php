<?php
class DownloadHelper
{
	static public function getCategoryTree()
	{
		global $tree;
		global $root_id;

		$root_id = DownloadManager::where('parent_id',null)
										->first()['id'];
		$tree = '';

		function recursiveTree($parent_id)
		{
			/*
			 * Recursively find the category tree
			 */	
			global $tree,$root_id;
			$children = DownloadManager::where('parent_id',$parent_id)
										->get();

			$tree .= ($parent_id==$root_id) ? '<ul class="sidebar-menu">' : '<ul class="treeview-menu">';
							
			foreach($children as $child)
			{
				if ($child['mime_type'] == EasyDriveAPI2::$folder_mime_type)
				{
					$tree .= ($parent_id==$root_id) ? '<li class="treeview">' : '<li>';
					$tree .= '<a href="' . URL::route('download-manager-frontend-files',[$child['id'],$child['google_file_id']]) . '">';
					$tree .= $child['filename'];
					if(DownloadManager::where('parent_id',$child['id'])
										->where('mime_type',EasyDriveAPI2::$folder_mime_type)
										->count())
					{
						$tree .= '<i class="fa fa-angle-left pull-right"></i>';
						$tree .= '</a>';
						recursiveTree($child['id']);	
					}
					else
					{
						$tree .= '</a>';
					}
					$tree .= '</li>';
				}
			}
			$tree .= '</ul>';

		}
		
		recursiveTree($root_id);
		return $tree;

		
	}

	/*public static function getFileIcon($filename)
	{
		preg_match('/[.].*$/', $filename, $extension);
		if(isset($extension[0]))
		{
	    	if($extension[0]=='.zip' || $extension[0]=='.rar' || $extension[0]=='.tar.gz' || $extension[0]=='.tar.7z' || $extension[0]=='.tar.bz2')
	      		return '<i class="fa fa-fw fa-file-zip-o"></i>';
	      	elseif($extension[0]=='.doc' || $extension[0]=='.docx')
	      		return '<i class="fa fa-fw fa-file-word-o"></i>';
	      	elseif($extension[0]=='.pdf')
	      		return '<i class="fa fa-fw fa-file-pdf-o"></i>';
	      	elseif($extension[0]=='.xls' || $extension[0]=='.csv')
	      		return '<i class="fa fa-fw fa-file-excel-o"></i>';
	      	else
	      		return '<i class="fa fa-fw fa-file-zip-o"></i>';
	    }  	
	    else
	    	return '<i class="fa fa-fw fa-file-zip-o"></i>';
	}*/
	
	

	public static function getFileIcon($mime_type)
	{
		if($mime_type == 'application/zip')
			return '<i class="fa fa-fw fa-file-zip-o"></i>';
		elseif($mime_type=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'
				|| $mime_type=='application/msword')
			return '<i class="fa fa-fw fa-file-word-o"></i>';
		elseif($mime_type=='application/pdf')
			return '<i class="fa fa-fw fa-file-pdf-o"></i>';
		elseif($mime_type='application/vnd.ms-excel')
			return '<i class="fa fa-fw fa-file-excel-o"></i>';
		else
			return '<i class="fa fa-fw fa-file-zip-o"></i>';
	}
	
	public static function getConfig($config_file='')
	{
		$config_file = ($config_file=='')?app_path().'/modules/'.'download-manager'.'/config.json':$config_file;
		$config = json_decode(
						File::get($config_file), 
						true
					);

		return $config;

	}

	public static function getDownloadLink($child)
	{
		return URL::route('download-manager-backend-file-download',[$child['id'],$child['google_file_id']]);
	}
	
	
}