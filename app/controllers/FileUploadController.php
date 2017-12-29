<?php

class FileUploadController extends Controller
{
	private $validExtensions;
	public $destination;
	private $size_limit;
	private $originalFileName;
	private $filename;

	public function __construct($destination = '', $valid_extensions = array(), $size_limit = 0, $filename = '')
	{
		$this->validExtensions = count($valid_extensions) ?  $valid_extensions : array('jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf');
		$this->size_limit = (int) $size_limit ? $size_limit : 75161927680;
		$this->destination = strlen(trim($destination)) ? $destination : base_path().'/public/uploaded';
		$this->filename = $filename;
	}

	public function setValidExtensions()
	{

	}

	public function getValidExtensions()
	{

	}

	public function setSizeLimit()
	{

	}

	public function getSizeLimit()
	{

	}

	public function uploadFile($file, $destination_name = '') //of type Input::file('filename')
	{
		$destination_name = strlen(trim($destination_name)) ? trim($destination_name) : $this->destination;
		if (($file->isValid()))
		{
		   if(($this->checkExtension($file)))
		   {
		   		if($this->checkSize($file))
		   		{
		   			if(strlen(trim($this->filename)))
		   			{
		   				$filenameToStore = $this->filenameRandomizer('', $this->filename, false);
		   			}
		   			else
		   			{
		   				do 
			   			{
			   			
			   				$filenameToStore = $this->filenameRandomizer($file->getClientOriginalExtension(), $basename = '');
			   			
			   			}while($this->checkIfFileExists($destination_name, $filenameToStore));	
		   			}
		   			
		   			//move file to destination folder
		   			try
		   			{
		   				$file->move($destination_name, $filenameToStore);
		   				chmod($destination_name.'/'.$filenameToStore, 0755);

		   				$status = "success";
		   				$message = "File Successfully uploaded";
		   				$original_name = $file->getClientOriginalName();
		   				$uploaded_name = $filenameToStore;

		   				return json_encode(array('status' => $status, 'message' => $message, 'original_name' => $original_name, 'uploaded_name' => $filenameToStore));

		   			}
		   			catch(Exception $e)
		   			{
		   				$status = 'error';
		   				$message = $e->getMessage();
		   			}
		   		}
		   		else
		   		{
		   			$status = 'error';
		   			$message = 'Exceeds size limit of '.$this->size_limit.' kb';
		   		}
		   }
		   else
		   {
		   		$status = 'error';
		   		$message = 'only '.implode(', ', $this->validExtensions).' allowed';
		   }
		}
		else
		{
			$status = 'error';
			$message = 'Invalid File';
		}

		return json_encode(array('status' => $status, 'message' => $message));
		//check extendsion
	}

	private function checkExtension($file)
	{
		$extension = $file->getClientOriginalExtension();
		
		if(in_array($extension, $this->validExtensions))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function checkSize($file)
	{
		$fileSize = $file->getSize();
		//>getMimeType();

		if($fileSize > $this->size_limit)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	private function getOriginalFileName($file)
	{
		$this->originalFileName = $file->getClientOriginalName();
		return $this->originalFileName;
	}

	public function filenameRandomizer($extension, $basename = '', $randomize = true)
	{
		if($randomize)
		{
			$temp = date('U');
			$temp = base64_encode($temp).uniqid();
			$temp = $basename.'_'.$temp.'.'.$extension;	
		}
		else
		{
			$temp = $basename;
		}
		
		return $temp;
	}

	public function checkIfFileExists($destination_name, $filename)
	{
		$checkFilename = $destination_name.'/'.$filename;

		return file_exists($checkFilename);
	}


}