<?php
class BookCopiesHelper
{
	/**
	 * Takes an array of IDs and checks whether they are unique
	 */
	public static function validateIds($ids,$except=array())
	{
		/**
		 * Generate the error message given the non-unique book ids (as an array)
		 * E.g: if $id=[1], error is "ID 1 already exists"
		 * E.g: if $id=[1,2], error is "IDs 1,2 already exist"
		 */
		function generateNonUniqueError($non_unique_book_ids)
		{

			if(!empty($non_unique_book_ids))
			{
				$book_id_error = (sizeof($non_unique_book_ids)>1) ? 'IDs ' : 'ID ';
				foreach($non_unique_book_ids as $id)
				{
			  		$book_id_error .= $id.',';
				}
			
				$book_id_error = rtrim($book_id_error,',');
				$book_id_error .= (sizeof($non_unique_book_ids)>1) ? ' already exist' : ' already exists';
				return $book_id_error;
			}
			else
			{
				return '';
			}
		}

		$result = array('status'=>'success', 'data'=>new Illuminate\Support\MessageBag);
		//get the id(s) that already exist
		$non_unique_book_ids = HelperController::findNonUniques($ids,'BookCopies','book_id');
		if(!empty($non_unique_book_ids))
		{
			$result['status'] = 'error';//validation error: we already have that book id registered!
			$result['data']->getMessageBag()->add('non_unique_book_ids', generateNonUniqueError($non_unique_book_ids));
		}
		return $result;
	}

	/**
	 * This is a wrapper function wrapping the validateWithExistingIds() and validateAvailabeIds()
	 * Used for checking if the book_ids can be assigned
	 */
	public static function validateForAssignment($ids)
	{
		// check if the books ids are not already taken
		$result = array('status'=>'success','data'=>new Illuminate\Support\MessageBag);
		
		$result_book_ids = BookCopiesHelper::validateWithExistingIds($ids); // validates if each id is available
		if($result_book_ids['status']=='error')
		{
			// if some ids have not been registered, return error right away
			return $result_book_ids;
		}
		// otherwise check if ids are available
		$result_book_ids = BookCopiesHelper::validateAvailabeIds($ids); // validates if each id is available
		return $result_book_ids;
	}

	/**
	 * This is a wrapper function wrapping the validateWithExistingIds() and validateBorrowedIds()
	 * Used for checking if the book_ids can be returned by the student
	 */
	public static function validateForReturn($ids,$student_id)
	{
		// check if the books ids are not already taken
		$result = array('status'=>'success','data'=>new Illuminate\Support\MessageBag);
		
		$result_book_ids = BookCopiesHelper::validateWithExistingIds($ids); // validates if each id is available
		if($result_book_ids['status']=='error')
		{
			// if some ids have not been registered, return error right away
			return $result_book_ids;
		}
		// otherwise check if ids are available
		$result_book_ids = BookCopiesHelper::validateBorrowedIds($ids,$student_id); // validates if each id is available
		return $result_book_ids;
	}


	/*
	 * Takes an array IDs and checks whether they already exist
	 */
	public static function validateWithExistingIds($ids,$except=array())
	{
		/*
		 * Generate the error message given the unique book ids (as an array)
		 * E.g: if $id=[1], error is "ID 1 not found"
		 * E.g: if $id=[1,2], error is "IDs 1,2 not found"
		 */
		function generateUniqueError($unique_book_ids)
		{

			if(!empty($unique_book_ids))
			{
				$book_id_error = (sizeof($unique_book_ids)>1) ? 'IDs ' : 'ID ';
				foreach($unique_book_ids as $id)
				{
			  		$book_id_error .= $id.',';
				}
			
				$book_id_error = rtrim($book_id_error,',');
				$book_id_error .= ' not found';
				return $book_id_error;
			}
			else
			{
				return '';
			}
		}

		$result = array('status'=>'success', 'data'=>new Illuminate\Support\MessageBag);
		//get the id(s) that already exist
		$unique_book_ids = HelperController::findUniques($ids,'BookCopies','book_id');
		if(!empty($unique_book_ids))
		{
			$result['status'] = 'error';//validation error: we already have that book id registered!
			$result['data']->getMessageBag()->add('unique_book_ids', generateUniqueError($unique_book_ids));
		}
		return $result;
	}

	
	/**
	 * Takes an array IDs and checks whether they are available
	 */
	public static function validateAvailabeIds($ids)
	{
		/**
		 * Generate the error message given the unique book ids (as an array)
		 * E.g: if $id=[1], error is "ID 1 is unavailable"
		 * E.g: if $id=[1,2], error is "IDs 1,2 are unavailble"
		 */
		function generateUnavailableError($unavailable_book_ids)
		{

			if(!empty($unavailable_book_ids))
			{
				$book_id_error = (sizeof($unavailable_book_ids)>1) ? 'IDs ' : 'ID ';
				foreach($unavailable_book_ids as $id)
				{
			  		$book_id_error .= $id.',';
				}
			
				$book_id_error = rtrim($book_id_error,',');
				$book_id_error .= ' not available';
				return $book_id_error;
			}
			else
			{
				return '';
			}
		}

		$result = array('status'=>'success', 'data'=>new Illuminate\Support\MessageBag);
		//get the id(s) that are already taken
		$unavailable_book_ids = array();
		foreach($ids as $id)
		{
			$model = new BookCopies;
			$record = DB::table($model::getTableName())
							->join(BooksAssigned::getTableName(),$model ->getTableName().'.book_id', '=', BooksAssigned::getTableName().'.book_copy_id')
							->where($model::getTableName().'.book_id',$id)
							->where(BooksAssigned::getTableName().'.returned_date','=', NULL)
							->select($model::getTableName().'.*',BooksAssigned::getTableName().'.returned_date as returned_date')
							->get();
			
			if($record)// when the book is already assigned but not returned
			{
				$unavailable_book_ids[] = $id;
			}
		}
		
		if(!empty($unavailable_book_ids))
		{
			$result['status'] = 'error';//validation error: we already have that book id registered!
			$result['data']->getMessageBag()->add('unavailable_book_ids', generateUnavailableError($unavailable_book_ids));
		}
		return $result;
	}

	/**
	 * Takes an array IDs and checks whether they are available
	 */
	public static function validateBorrowedIds($ids,$student_id)
	{
		/**
		 * Generate the error message given the book ids (as an array) not borrowed by the student
		 * E.g: if $id=[1], $student_id=3 error is "ID 1 not borrowed by Student 3"
		 * E.g: if $id=[1,2], $student_id=3 error is "IDs 1,2 not borrowed by Student 3"
		 */
		function generateNotBorrowedByStudentError($unavailable_book_ids, $student_id)
		{

			if(!empty($unavailable_book_ids))
			{
				$book_id_error = (sizeof($unavailable_book_ids)>1) ? 'IDs ' : 'ID ';
				foreach($unavailable_book_ids as $id)
				{
			  		$book_id_error .= $id.',';
				}
			
				$book_id_error = rtrim($book_id_error,',');
				$book_id_error .= ' not borrowed by Student '.$student_id;
				return $book_id_error;
			}
			else
			{
				return '';
			}
		}

		$result = array('status'=>'success', 'data'=>new Illuminate\Support\MessageBag);
		//get the id(s) that are already taken
		$not_borrowed_by_student_book_ids = array();
		foreach($ids as $id)
		{
			$model = new BookCopies;
			$record = DB::table($model::getTableName())
							->join(BooksAssigned::getTableName(),$model ->getTableName().'.book_id', '=', BooksAssigned::getTableName().'.book_copy_id')
							->where($model::getTableName().'.book_id',$id)
							->where(BooksAssigned::getTableName().'.returned_date','=', NULL)
							->where(BooksAssigned::getTableName().'.student_id',$student_id)
							->select($model::getTableName().'.*',BooksAssigned::getTableName().'.returned_date as returned_date')
							->get();
			
			if(!$record)// when the book is not borrowed by the student
			{
				$not_borrowed_by_student_book_ids[] = $id;
			}
			
		}
		
		if(!empty($not_borrowed_by_student_book_ids))
		{
			$result['status'] = 'error';//validation error: we already have that book id registered!
			$result['data']->getMessageBag()->add('not_borrowed_by_student_book_ids', generateNotBorrowedByStudentError($not_borrowed_by_student_book_ids,$student_id));
		}
		return $result;
	}

	/**
	 * Get the ids of copies of a book with given books_id in csv
	 */
	public static function getBookCopiesIdCsv($books_id)
	{
		$copies = BookCopies::select('book_id')
							->where('books_id',$books_id)
							->get();
		$id_array = array();
		foreach($copies as $copy)					
		{
			$id_array[] = $copy['book_id'];
		}
		return implode(',', $id_array);
	} 

	
}