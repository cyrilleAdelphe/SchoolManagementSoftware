<?php


class BookCopies extends BaseModel
{
	protected $table = 'book_copies';
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $model_name = 'BookCopies';

	public $createRule = [
							'books_id' => ['required'],
							'book_id'	=> ['required','unique:book_copies,book_id']
						];

	public $updateRule = [
							'books_id' => ['required'],
							'book_id'	=> ['required','unique:book_copies,book_id']
						];
}