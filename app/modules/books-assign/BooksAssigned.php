<?php


class BooksAssigned extends BaseModel
{
	protected $table = 'books_assigned';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'BooksAssigned';

	public $createRule = [
							'title1' 		=> ['exists:books,title','required_without:book_ids'],
							'title2' 		=> ['exists:books,title'],
							'title3' 		=> ['exists:books,title'],
							'title4' 		=> ['exists:books,title'],
							/* Library-alphanumeric-ids-v1-code-here
							Remove this code
							'book_ids'	=> ['regex:/^(((([\d]{1,})+[,]){1,})*([\d]{1,}))$/','required_without:title1'],
							*/
							//'student_id'	=> ['required','exists:students,student_id'],
							'assigned_date'	=> ['required','regex:#^[\d]{1,2}/[\d]{2}/[\d]{4}$#']
						];

	public $updateRule = [
							'title1' 		=> ['exists:books,title','required_without:book_ids'],
							'title2' 		=> ['exists:books,title'],
							'title3' 		=> ['exists:books,title'],
							'title4' 		=> ['exists:books,title'],
							/* Library-alphanumeric-ids-v1-code-here
							Remove this code
							'book_ids'	=> ['regex:/^(((([\d]{1,})+[,]){1,})*([\d]{1,}))$/','required_without:title1'],
							*/
							//'student_id'	=> ['required','exists:students,student_id'],
							'assigned_date'	=> ['required_without:returned_date','regex:#^[\d]{1,2}/[\d]{2}/[\d]{4}$#'],
							'returned_date'	=> ['required_without:assigned_date','regex:#^[\d]{1,2}/[\d]{2}/[\d]{4}$#']
						];

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));

		/*$result->join(Users::getTableName(),$this->getTableName().'.student_id', '=', Users::getTableName().'.user_details_id')
				->join(Student::getTableName(),$this->getTableName().'.student_id','=',Student::getTableName().'.student_id');*/
				
		//$result = $result->where(Users::getTableName().'.role', 'student');
			

		$result->join(Books::getTableName(),$this->getTableName().'.books_id', '=', Books::getTableName().'.id')
				->select($this->getTableName().'.*',
							//Users::getTableName().'.name as student_name',
							//Users::getTableName().'.username',
							//Users::getTableName().'.email as student_email',
							Books::getTableName().'.title as book_title'
							//Student::getTableName().'.current_class_id',
							//Student::getTableName().'.current_section_code',
							//Student::getTableName().'.current_roll_number'
							);


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
				if ($col=='title')
				$result = $result->where(Books::getTableName().'.title', 'LIKE', '%'.$query_vals[$index].'%');
				elseif ($col=='username')
					$result = $result->where(Users::getTableName().'.username', 'LIKE', '%'.$query_vals[$index].'%');
				elseif ($col=='returned_date' && strtolower($query_vals[$index]) == 'not')
					$result = $result->whereNull('returned_date');
				else
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');
			}
			
		}
		
		//}

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
}