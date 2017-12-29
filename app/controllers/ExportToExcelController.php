<?php

class ExportToExcelController extends Controller
{
	public function getExportExcel()
	{
		$input = Input::all();
		$filename = isset($input['file_name']) ? $input['file_name'] : 'Excel Report';
		$input_data = Input::all();
	
		Excel::create($filename, function($excel) use ($input_data, $filename)
		{

			$excel->sheet("Sheet 1", function($sheet) use ($input_data)
			{
				$input_data['row'] = json_decode($input_data['json']);
				$i = 1;
				foreach($input_data['row'] as $r)
				{
					$row = [];
					foreach($r as $d)
					{
						$row[] =  $d;
					}
					$sheet->row(++$i, $row);		
					
				}
					
			});
		})->download('xls');
	}
}