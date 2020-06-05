<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BooksController extends BaseController
{
	protected $view = 'books.views.';

	protected $model_name = 'Books';

	protected $module_name = 'books';

	protected $role;

	public $columnsToShow = array(
									array
									(
										'column_name' 	=> 'title',
										'alias'			=> 'Title'
									),
									array
									(
										'column_name' 	=> 'author',
										'alias'			=> 'Author'
									),
									array
									(
										'column_name' 	=> 'in_books',
										'alias'			=> 'In'
									),
									array
									(
										'column_name' 	=> 'out_books',
										'alias'			=> 'Out'
									),
									array
									(
										'column_name' 	=> 'rack_number',
										'alias'			=> 'Rack No'
									),
									array
									(
										'column_name' 	=> 'category_name',
										'alias'			=> 'Category Name'
									),
									array
									(
										'column_name' 	=> 'max_holding_days',
										'alias'			=> 'Holding'
									)

								 );

	public function getDownloadBooksBarcode($id)
	{	

		AccessController::allowedOrNot($this->module_name, 'can_download');

		$book_id = $id;

		$book_details = DB::table('book_copies')
						->join('books', 'books.id','=','book_copies.books_id');

		if($id)
		{
				$book_details = $book_details->where('book_copies.books_id', $id);
		}
						
						
						/*->where('is_generated_bar_code', 'yes')*/
		$book_details = $book_details->select('title', 'book_copies.*')
						->get();

		/*echo '<pre>';
		print_r($book_details);
		die();*/

		if(!$book_details)
		{
			return Redirect::back()->with('error-msg', 'BarCode not available, please generate first');
		}
		return View::make($this->view. 'generate-barcode')
						->with('book_details', $book_details);

	}

	public function generateBarCodePost()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create_barcode');

		ini_set('max_execution_time', 300); 

			//$bar = new DNS1D();

			try
			{
				DB::connection()->getPdo()->beginTransaction();


				$input = Input::all();


				if($input['book_id'] == "all")
				{
					$data_to_generate = DB::table('book_copies')
									->join('books', 'books.id', '=' , 'book_copies.books_id')
									->select('books.title', 'book_copies.*')
									->get();	
				}
				else
				{

					$data_to_generate = DB::table('book_copies')
									->join('books', 'books.id', '=' , 'book_copies.books_id')
									->where('books_id', $input['book_id'])
									->select('books.title', 'book_copies.*')
									->get();

				}
				
				
				if(!count($data_to_generate))
				{
					return Redirect::back()
					->with('error-msg', 'Book Copies not found');
				}

				foreach ($data_to_generate as $key => $d)
				{
			
					/*$data_to_update = DB::table('book_copies')
									->where('book_id', $d->book_id)
									->update(['is_generated_bar_code' => 'yes']);*/

					$path = app_path('modules/books/all-books');
					
		  			if(!File::exists($path)) 
		  			{
		    			File::makeDirectory($path, $mode = 0777, true);

		 			}

		 			$book_path = app_path('modules/books/all-books/'. $d->title . '-'.$d->books_id.'/');
		 			DNS1D::setStorPath($book_path);

		 			if(!File::exists($book_path)) 
					{

						File::makeDirectory($book_path, $mode = 0777, true);
					}


					echo DNS1D::getBarcodePNGPath($d->book_id, "C128");	
					//$url = URL::route('generate-bar-codes-from-books', $d->book_id);
				}

				DB::connection()->getPdo()->commit();
				return Redirect::back()->with('success-msg', 'Bar Code Created Succesfully');

			}

		catch(Exception $e)
		{	
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}	
			
		
	}



	public function getGenerateBarCodesfromBooks($book_id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_create_barcode');
		
		$book_id = $book_id;
		
		return View::make($this->view. 'generate-barcode')->with('book_id', $book_id);
	}

	public function getGenerateBarCodeView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create_barcode');

		$books = DB::table('books')->get();
		/*echo '<pre>';
		print_r($books);
		die();*/
		
		return View::make($this->view. 'generate-bar-codes-get')
						->with('books', $books);
	}


	public function postUploadBooksFromLibrary()
	{
		AccessController::allowedOrNot($this->module_name, 'can_upload_books');

		ini_set('max_execution_time', 300); 

		$validator = Validator::make(Input::all(), array(
            'excel_file' => 'required|mimes:xls,xlsx'
                             
            ));

        if($validator->fails()) {
            return Redirect::back()
                             ->withInput()
                             ->withErrors($validator);
        }
		
		$reader = Excel::load(Input::file('excel_file'))
										->get();


		try
		{
			DB::connection()->getPdo()->beginTransaction();

			foreach($reader as $row)
			{
				// convert object to array

				$data = json_decode(json_encode($row), true);
				foreach($data as $key => $value)
				{
					if ($value == null)
					{
						$data[$key] = '';
					}

				}

					/*echo '<pre>';
					print_r($row);
					die();*/
					$data_to_store_in_book_category_table =  BookCategories::firstOrNew(array('title' => $row['category_title']));			
					$data_to_store_in_book_category_table->description = $row['category_description'];
					$data_to_store_in_book_category_table->rack_number = $row['rack_number'];	
					$data_to_store_in_book_category_table->save();

					$category_id = $data_to_store_in_book_category_table->id;

					$data_to_store_in_books_table =  Books::firstOrNew(array('title' => $row['book_title'] , 'author' => $row['author']));
					$data_to_store_in_books_table->category_id = $category_id;
					/*$data_to_store_in_books_table->author = $row['author'];*/
					$data_to_store_in_books_table->published_date = $row['published_date'];
					$data_to_store_in_books_table->price = $row['price'];
					$data_to_store_in_books_table->no_of_copies = $row['no_of_copies'];

					$data_to_store_in_books_table->max_holding_days = $row['max_holding_days'];
					$data_to_store_in_books_table->description = $row['description'];
					$data_to_store_in_books_table->save();

					
					$books_id = $data_to_store_in_books_table->id;

					$temp = $row['no_of_copies'];
					
					for($i = 1; $i <= $temp; $i++)
					{
						
						/*$current_timestamp = Carbon::now()->timestamp; 
						$current_timestamp = $current_timestamp + $i;*/
						$data_to_store_in_book_copies_table = new BookCopies;
						$data_to_store_in_book_copies_table->books_id = $books_id;    /*BookCopies::firstOrNew(array('books_id' => $books_id));*/
						$data_to_store_in_book_copies_table->book_id = 0;
						$data_to_store_in_book_copies_table->save();
						$data_to_store_in_book_copies_table->book_id = $data_to_store_in_book_copies_table->id;
						//$data_to_store_in_book_copies_table->save();

						//$data_to_store_in_book_copies_table->book_id .= $data_to_store_in_book_copies_table->id;

						/*$string = (string) $data_to_store_in_book_copies_table->book_id;*/
 
						$max_length = 12;

						$num_length = strlen((string)$data_to_store_in_book_copies_table->book_id);

						$difference = $max_length - $num_length;

						$random_number = '';

						for($k = 0; $k < $difference ; $k++)
						{
							$random_number .= mt_rand(0, 9);

						}
						

							$data_to_store_in_book_copies_table->book_id= $random_number.$data_to_store_in_book_copies_table->book_id;
							

							$data_to_store_in_book_copies_table->save();
						/*$unique_ids =[];
						$unique_ids[$i] = $data_to_store_in_book_copies_table->book_id;

											
						$path = app_path('modules/books/all-books');
			
			  			if(!File::exists($path)) 
			  			{
			    			File::makeDirectory($path, $mode = 0777, true);
 
			 			}


			 			$book_path = app_path('modules/books/all-books/'. $row['book_title'] . '-'.$books_id);

			 			if(!File::exists($book_path)) 
		  				{

		    				File::makeDirectory($book_path, $mode = 0777, true);
		    			}


		    				$conv = new \Anam\PhantomMagick\Converter();
							
		 					$url = URL::route('generate-bar-codes', $unique_ids[$i]);
		 					
		 					

		 					$options = [
							  'width' => 670,
							  'quality' => 100,
							  'height' => 700,
							  'zoomfactor' => 3
							];

							$conv->imageOptions($options);
			
							$conv->source($url)
						    	 ->toPng()
						    	 ->save($book_path.'/'.$unique_ids[$i].'.png');*/
					}	
					
						
			}

			DB::connection()->getPdo()->commit();
			return Redirect::back()->with('success-msg', 'Successfully Uploaded');

		}

		catch(Exception $e)
		{	
			DB::connection()->getPdo()->rollback();
			Session::flash('error-msg', $e->getMessage());
			return Redirect::back();
		}	
	
	}
	

	public function getCreateView()
	{
		AccessController::allowedOrNot($this->module_name, 'can_create');
		$categories = DB::table(BookCategories::getTableName())
				  ->where('is_active', 'yes')
				  ->select('id', 'title')
				  ->lists('title', 'id');




		
		return View::make($this->view . 'create')
				->with('categories',$categories)
				->with('module_name', $this->module_name)
				->with('current_user', $this->current_user)
				->with('role',$this->role)
				->with('actionButtons', $this->getActionButtons());
	}

	public function postCreateView()
	{
	AccessController::allowedOrNot($this->module_name, 'can_create');
		/*
		 * Some confusing variables / indexes / table fields
		 * index 'book_ids' : ids of the individual copies of the book
		 * field 'book_id': unique id field of the table book_copies (not the primary key)
		 * variable $books_id: id of the book from the table books
		 */
		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		/*echo '<pre>';
		print_r($data);
		die(); */

		$result = $this->validateInput($data);
		
		
		$book_ids_array = array();
		// if we know that we have a valid non-empty string of book_ids, check if each is unique
		if(!empty($data['book_ids']) && ($result['status']!='error' || !$result['data']->has('book_ids')) )
 		{
 			//$data['book_ids'] = HelperController::csvRangeToCsv($data['book_ids']);

			$data['book_ids'] = str_replace(' ', '', $data['book_ids']);
 			$book_ids_array = array_unique(explode(',', $data['book_ids']));
 			$result_book_ids = BookCopiesHelper::validateIds($book_ids_array); // validates the uniqueness of each id
 			if($result_book_ids['status']=='error')
 			{
 				$result['status'] = 'error';
 				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;
 				// only non_unique_book_ids error can occur after the first validation from validateInput method
 				if($result_book_ids['data']->has('non_unique_book_ids'))
 				{
 					$result['data']->getMessageBag()->add('non_unique_book_ids',$result_book_ids['data']->first('non_unique_book_ids'));
 				}
 			}
 		}
		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-create-get')
						->withInput()
						->with('errors', $result['data']);
		}
		
		
		if(!empty($data['book_ids']))
		{
			$data['no_of_copies'] = sizeof($book_ids_array);
		}


		if(Input::hasFile('image'))
		{
			$image_controller = new FileUploadController(Input::file('image'));
			
			$data['image'] = $image_controller->saveImage($strict = false, (base_path().'/public/sms/assets/img/books'));
			$image_controller->generateThumbnail($aspect_ratio = false, $resize_parameter = array('height' =>200, 'width' => 150), (base_path().'/public/sms/assets/img/books/small'), $data['image']);
			$image_controller->generateThumbnail($aspect_ratio = true, $resize_parameter = array('compression_ratio' => 2), (base_path().'/public/sms/assets/img/books/medium'), $data['image']);
		}

		 if ( Input::hasFile('image')) {

            $file = Input::file('image');
            $name = time().'-'.$file->getClientOriginalName();
            $file = $file->move('public/sms/assets/img/books', $name);
            $data['image'] = $name;
        }

		else
		{
			$data['image'] = '';
		}
		try
		{
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->storeInDatabase($data);

			if(!empty($data['book_ids']))
			{
				foreach ($book_ids_array as $book_id) 
				{
					BookCopies::create([
										'books_id' 	=> $id, // id of Books Model
										'book_id'	=> $book_id // id of the copy of the book
									]);
				}

			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'create', $param, $msg);
	}

	public function getEditView($id)
	{
		AccessController::allowedOrNot($this->module_name, 'can_edit');
		$categories = DB::table(BookCategories::getTableName())
				  ->where('is_active', 'yes')
				  ->select('id', 'title')
				  ->lists('title', 'id');

		$book_ids = BookCopiesHelper::getBookCopiesIdCsv($id);
		
		$model = $this->model_name;
		$model = new $model;
		$data = $model->getEditViewData($id);
		
		return View::make($this->view.'edit')
					->with('categories',$categories)
					->with('book_ids',$book_ids)
					->with('module_name', $this->module_name)
					->with('role', $this->role)
					->with('current_user', $this->current_user)
					->with('data', $data);
					//->with('actionButtons', $this->getActionButtons());
	}

	public function postEditView($id)
	{
	AccessController::allowedOrNot($this->module_name, 'can_edit');
		/*
		 * Some confusing variables / indexes / table fields
		 * index 'book_ids' : ids of the individual copies of the book
		 * field 'book_id': unique id field of the table book_copies (not the primary key)
		 * variable $books_id: id of the book from the table books
		 */

		$success = false;
		$msg = '';
		$param = array('id' => 0);

		$data = Input::all();
		$prabal_book_ids = $data['book_ids'];

		$result = $this->validateInput($data,true);
		
		
//			die('here');

		$book_ids_array = array();
		// if we know that we have a valid non-empty string of book_ids, check if each is unique
		if(!empty($data['book_ids']) && ($result['status']!='error' || !$result['data']->has('book_ids')) )
 		{
 			$data['book_ids'] = HelperController::csvRangeToCsv($data['book_ids']);
 			$book_ids_array = explode(',', $data['book_ids']);
 			//the ids that are already associated to the current books_id
 			$except_array = array();
 			$except = BookCopies::select('book_id')->where('books_id',$id)->get();
 			foreach($except as $existing) $except_array[] = $existing['book_id'];
 			
 			$new_ids = array_diff($book_ids_array , $except_array);
 			
 			
 			$result_book_ids = BookCopiesHelper::validateIds($new_ids); // validates the uniqueness of each id
 			if($result_book_ids['status']=='error')
 			{
 				$result['status'] = 'error';
 				if(empty($result['data'])) $result['data'] = new Illuminate\Support\MessageBag;
 				// only non_unique_book_ids error can occur after the first validation from validateInput method
 				if($result_book_ids['data']->has('non_unique_book_ids'))
 				{
 					$result['data']->getMessageBag()->add('non_unique_book_ids',$result_book_ids['data']->first('non_unique_book_ids'));
 				}
 			}
 		}
 		
 		
		if($result['status'] == 'error')
		{
			Session::flash('error-msg', ConfigurationController::translate('some validation errors have occured'));
			return Redirect::route($this->module_name.'-edit-get',[$id])
						->withInput()
						->with('errors', $result['data']);
		}
		
		$data['book_ids'] = $prabal_book_ids;
		$book_ids_array = explode(',', $data['book_ids']);
		
		if(!empty($data['book_ids']))
		{
			$data['no_of_copies'] = sizeof($book_ids_array);
		}

		if(Input::hasFile('image'))
		{
			$image_controller = new FileUploadController2(Input::file('image'));
			
			$data['image'] = $image_controller->saveImage($strict = false, (base_path().'/public/sms/assets/img/books'));
			$image_controller->generateThumbnail($aspect_ratio = false, $resize_parameter = array('height' =>200, 'width' => 150), (base_path().'/public/sms/assets/img/books/small'), $data['image']);
			$image_controller->generateThumbnail($aspect_ratio = true, $resize_parameter = array('compression_ratio' => 2), (base_path().'/public/sms/assets/img/books/medium'), $data['image']);
		}
		else
		{
			$data['image'] = Books::find($id)['image'];
		}
		try
		{
			
			
			DB::connection()->getPdo()->beginTransaction();

			$id = $this->updateInDatabase($data);

			BookCopies::where('books_id',$id)->delete();

			if(!empty($data['book_ids']))
			{
				foreach ($book_ids_array as $book_id) 
				{
					BookCopies::create([
										'books_id' 	=> $id, // id of Books Model
										'book_id'	=> $book_id // id of the copy of the book
									]);
				}

			}

			$success = true;
			$msg = 'Record successfully created';
			$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			$success = false;
			$msg = $e->getMessage();
		}
		
		return $this->redirectAction($success, 'edit', $param, $msg);
	}

	public function postDelete()
	{
	AccessController::allowedOrNot($this->module_name, 'can_delete');
		$model = new $this->model_name;
		$id = Input::get('id');
		$record = $model->find($id);
		if($record)
		{
			File::delete(base_path().'/public/sms/assets/img/books/' . $record->image);
			File::delete(base_path().'/public/sms/assets/img/books/small/' . $record->image);
			File::delete(base_path().'/public/sms/assets/img/books/medium/' . $record->image);
		}
		return parent::postDelete();
	}
}