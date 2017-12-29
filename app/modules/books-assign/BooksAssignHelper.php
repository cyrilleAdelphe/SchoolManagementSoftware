<?php

class BooksAssignHelper
{
	public static function getDueDays($assign_id)
	{
		$assigned = BooksAssigned::find($assign_id);
		// if already returned, no due days
		if($assigned['returned_date']) return 0;
		return HelperController::getNumberOfDays(date('Y-m-d'), $assigned['due_date']);
	}
}