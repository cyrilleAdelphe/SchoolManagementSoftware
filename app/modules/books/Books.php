<?php

//validates 1,3,5 or 1
//^(((([\d]{1,})+[,]){1,})*([\d]{1,}))$
//regex_csv = (((([\d]{1,})+[,]){1,})*([\d]{1,}))

//validates 1-10
//(^(([\d]{1,})+[-]+([\d]{1,}))$)
//regex_range = (([\d]{1,})+[-]+([\d]{1,}))
//regex_range = regex_number+[-]+regex_number

//only numbers
//regex_number = [\d]{1,}

//validates 1,5-7,7-8 or 10-12 or 1,3,5 or 1
//^(((([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,})))+[,]){1,})*([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}))))))$
//regex_final = ^(((regex_number | regex_range)+[,])*(regex_number | regex_range))$

class Books extends BaseModel
{
	protected $table = 'books';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'Books';

	public $createRule = [
							'title' 		=> ['required'],
							'category_id'	=> ['required','integer'],
							'price' 		=> ['required','integer','min:1'],
							'author' 		=> ['required'],
							'published_date'=> ['required','date_format:Y-m-j'],
							'max_holding_days'=> ['required','integer'],
							'description' 	=> [],
							/*'book_ids' 		=> ['regex:/^(((([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,})))+[,]){1,})*([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}))))))$/'], */
							'image'			=> ['mimes:jpeg,png,bmp', 'max:5000']
						];

	public $updateRule = [
							'title' 		=> ['required'],
							'price' 		=> ['required','integer','min:1'],
							'author' 		=> ['required'],
							'published_date'=> ['required','date_format:Y-m-j'],
							'max_holding_days'=> ['required','integer'],
							'description' 	=> [],
							/*'book_ids'		=> ['regex:/^(((([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,})))+[,]){1,})*([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}|(([\d]{1,})+[-]+([\d]{1,}))))))$/'], */
							'image'			=> ['mimes:jpeg,png,bmp', 'max:5000']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));
		
		$result = $result->join(BookCategories::getTableName(),$this->getTableName().'.category_id', '=', BookCategories::getTableName().'.id');
		
		$result = $result->select($this->getTableName().'.*',
									BookCategories::getTableName().'.title as category_title');
		
		foreach($result->get() as $key=>$row)
		{
			$book_id = $row->id;
			$row->out_books = 0;
		}

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
			
				if($col=='out_books' || $col=='in_books')
					$result = $result->where($col, 'LIKE', '%'.$query_vals[$index].'%');
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			
			}
		}

		if(isset($queryString['orderBy']))
		{
			$result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		
		
		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getViewViewData($id)
	{
		$result = parent::getViewViewData($id);
		$result->book_ids = implode(', ',  BookCopies::where('books_id', $id)
																	->lists('book_id')
																);
		return $result;
	}

}