<?php

class TemplateHelperController
{
	public function getAllPositions()
	{
		$result = array();
		$result = Position::where('is_active' , 'yes')
							->get(array('position_name', 'id'));

		return $result;
	}

	public function getSelectedPositions($template_id)
	{
		$result = array();
		$result = DB::table(PositionTemplate::getTableName())
					->join(Position::getTableName(), Position::getTableName().'.id', '=', PositionTemplate::getTableName().'.position_id')
					->where(PositionTemplate::getTableName().'.template_id', $template_id)
					->where(Position::getTableName().'.is_active', 'yes')
					->where(PositionTemplate::getTableName().'.is_active', 'yes')
					->lists('sort_order', 'position_id');

		return $result;
	}
}