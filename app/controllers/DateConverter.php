<?php

use Carbon\Carbon;

class DateConverter
{
	//TODO: Find a good place for these magic numbers
	private $bs_date_eq='09/17/2000'; //base Nepali date
	private $ad_date_eq='01/01/1944'; //base English date
	private $bs= array(); //an array of arrays for the number of days in each month of Nepali years
	
	private $max_bs_year = null;//this is updated by method updateMaxValidYearBS() in the constuctor
	
	//These are updated by updateMaxValidDateAD() in the constructor
	private $max_ad_date = null;
	

	public function __construct()
	{
		//TODO: Find a good place for these magic numbers
		$this->bs[2000]=array(30,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2001]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2002]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2003]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2004]=array(30,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2005]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2006]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2007]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2008]=array(31,31,31,32,31,31,29,30,30,29,29,31);$this->bs[2009]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2010]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2011]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2012]=array(31,31,31,32,31,31,29,30,30,29,30,30);$this->bs[2013]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2014]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2015]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2016]=array(31,31,31,32,31,31,29,30,30,29,30,30);$this->bs[2017]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2018]=array(31,32,31,32,31,30,30,29,30,29,30,30);$this->bs[2019]=array(31,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2020]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2021]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2022]=array(31,32,31,32,31,30,30,30,29,29,30,30);$this->bs[2023]=array(31,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2024]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2025]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2026]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2027]=array(30,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2028]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2029]=array(31,31,32,31,32,30,30,29,30,29,30,30);$this->bs[2030]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2031]=array(30,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2032]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2033]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2034]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2035]=array(30,32,31,32,31,31,29,30,30,29,29,31);$this->bs[2036]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2037]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2038]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2039]=array(31,31,31,32,31,31,29,30,30,29,30,30);$this->bs[2040]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2041]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2042]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2043]=array(31,31,31,32,31,31,29,30,30,29,30,30);$this->bs[2044]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2045]=array(31,32,31,32,31,30,30,29,30,29,30,30);$this->bs[2046]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2047]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2048]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2049]=array(31,32,31,32,31,30,30,30,29,29,30,30);$this->bs[2050]=array(31,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2051]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2052]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2053]=array(31,32,31,32,31,30,30,30,29,29,30,30);$this->bs[2054]=array(31,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2055]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2056]=array(31,31,32,31,32,30,30,29,30,29,30,30);$this->bs[2057]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2058]=array(30,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2059]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2060]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2061]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2062]=array(30,32,31,32,31,31,29,30,29,30,29,31);$this->bs[2063]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2064]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2065]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2066]=array(31,31,31,32,31,31,29,30,30,29,29,31);$this->bs[2067]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2068]=array(31,31,32,32,31,30,30,29,30,29,30,30);$this->bs[2069]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2070]=array(31,31,31,32,31,31,29,30,30,29,30,30);$this->bs[2071]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2072]=array(31,32,31,32,31,30,30,29,30,29,30,30);$this->bs[2073]=array(31,32,31,32,31,30,30,30,29,29,30,31);$this->bs[2074]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2075]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2076]=array(31,32,31,32,31,30,30,30,29,29,30,30);$this->bs[2077]=array(31,32,31,32,31,30,30,30,29,30,29,31);$this->bs[2078]=array(31,31,31,32,31,31,30,29,30,29,30,30);$this->bs[2079]=array(31,31,32,31,31,31,30,29,30,29,30,30);$this->bs[2080]=array(31,32,31,32,31,30,30,30,29,29,30,30);$this->bs[2081]=array(31,31,32,32,31,30,30,30,29,30,30,30);$this->bs[2082]=array(30,32,31,32,31,30,30,30,29,30,30,30);$this->bs[2083]=array(31,31,32,31,31,30,30,30,29,30,30,30);$this->bs[2084]=array(31,31,32,31,31,30,30,30,29,30,30,30);$this->bs[2085]=array(31,32,31,32,30,31,30,30,29,30,30,30);$this->bs[2086]=array(30,32,31,32,31,30,30,30,29,30,30,30);$this->bs[2087]=array(31,31,32,31,31,31,30,30,29,30,30,30);$this->bs[2088]=array(30,31,32,32,30,31,30,30,29,30,30,30);$this->bs[2089]=array(30,32,31,32,31,30,30,30,29,30,30,30);$this->bs[2090]=array(30,32,31,32,31,30,30,30,29,30,30,30);

		$this->updateMaxValidYearBS();
		$this->updateMaxValidDateAD();
	}

	/**
	 * Converts a date in BS to AD
	 * Returns false if the date is invalid or out of range
	 * The input and output date are in the format yyyy-mm-dd
	 */

	public function getCurrentMonthBs()
	{
		$current_month = $this->ad2bs(date('Y-m-d', time()));
		$current_month = explode('-', $current_month)[1];
		return $current_month;
	}

	public function bs2ad($bs_date)
	{
		try
		{
			$bs_date = $this->dashDateToSlashDate($bs_date);
			$days_count=$this->countBsDays($this->bs_date_eq,$bs_date);
			if ($days_count === false)
			{
				return false;
			}
			return $this->addAdDays($this->ad_date_eq,$days_count);
		}
		catch(Exception $e)
		{
			return false;
		}
			
	}

	/**
	 * Converts a date in AD to BS
	 * Returns false if the date is invalid or out of range
	 * The input and output date are in the format yyyy-mm-dd
	 */
	public function ad2bs($ad_date)
	{
		try
		{
			$ad_date = $this->dashDateToSlashDate($ad_date);
			$days_count=$this->countAdDays($this->ad_date_eq,$ad_date);
			if ($days_count === false)
			{
				die('invalid date');
				return false;
			}
			return $this->addBsDays($this->bs_date_eq,$days_count);
		}
		catch(Exception $e)
		{
			die('invalid date');
			return false;
		}
	}
	
	/**
	 * Count days between two AD dates
	 * date format: mm/dd/yyyy
	 */
	protected function countAdDays($start_date,$end_date)
	{
		//validate the dates
		if (!$this->checkAdDate($start_date) || !$this->checkAdDate($end_date))
		{
			return false;
		}

		$one_day=60*60*24;// no. of secs in a day (in js ms was used)
		$x=explode('/',$start_date);
		$y=explode('/',$end_date);
		$x[2]=(int)($x[2]);
		$x[1]=(int)($x[1]);
		$x[0]=(int)($x[0]);
		$y[2]=(int)($y[2]);
		$y[1]=(int)($y[1]);
		$y[0]=(int)($y[0]);

			

		$date1=strtotime($this->slashDateToDashDate($start_date));
		$date2=strtotime($this->slashDateToDashDate($end_date));
		$diff=ceil(($date2-$date1)/($one_day));
		return $diff;
	}

	/**
	 * Count days between two BS dates
	 */
	protected function countBsDays($start_date,$end_date)
	{
		//validate the dates
		if (!$this->checkBsDate($start_date) || !$this->checkBsDate($end_date))
		{
			return false;
		}

		$x=explode('/',$start_date);
		$y=explode('/',$end_date);
		$start_year=(int)($x[2]);
		$start_month=(int)($x[0]);
		$start_days=(int)($x[1]);
		$end_year=(int)($y[2]);
		$end_month=(int)($y[0]);
		$end_days=(int)($y[1]);

		$days=0;
		$i=0;

		/*Add days in all the year in between*/
		for($i=$start_year;$i<=$end_year;$i++)
		{
			$days+= $this->arraySum($this->bs[$i]);
		}
		/*Subtract the months from the first year before $start_month*/
		for($i=0;$i<$start_month;$i++)
		{
			$days-= $this->bs[$start_year][$i];
		}

		//TODO: Find out what the hell this is about
		$days+= $this->bs[$start_year][12-1];

		/*Subtract the months from the last year after $end_month*/
		for($i=$end_month-1;$i<12;$i++)
		{
			$days -=$this->bs[$end_year][$i];
		}
		/*Subtract days from $start_month before $start_days*/
		$days-= $start_days+1;
		/*Add days $end_days of $end_month*/
		$days+= $end_days-1;
		return $days;
	}

	/**
	 * Add days to an AD date and return the corresponding date (AD)
	 * date format mm/dd/yyyy
	 */
	protected function addAdDays($ad_date,$num_days)
	{
		return date('Y-m-d',strtotime($this->slashDateToDashDate($ad_date) . ' + '. $num_days.' days'));
		
	}

	/**
	 * Add days to a BS date and return the corresponding date (BS)
	 */
	protected function addBsDays($bs_date,$num_days)
	{
		$x=explode('/',$bs_date);
		$this->bs_year=(int)($x[2]);
		$this->bs_month=(int)($x[0]);
		$this->bs_days=(int)($x[1]);
		$this->bs_days+=$num_days;

		while($this->bs_days>$this->bs[$this->bs_year][$this->bs_month-1])
		{
			$this->bs_days-=$this->bs[$this->bs_year][$this->bs_month-1];
			$this->bs_month++;
			if($this->bs_month>12)
			{
				$this->bs_month=1;
				$this->bs_year++;
			}
		}
		return $this->bs_year.'-'.$this->bs_month.'-'.$this->bs_days;
	}

	//TODO: find if there is any built in functions for summing up an array
	/**
	 * Find sum of values of an array (the values of the array must be operable by '+' operator)
	 */
	protected function arraySum($arr)
	{
		$sum = 0;
		foreach($arr as $a)
		{
			$sum += $a;
		}
		return $sum;
	}

	//TODO: find if there is any built in function to find the maximum key in an array
	//TODO: alternately, check if there is any function to get keys of an array as an array and then find the max of it
	/**
	 * Get the  maximum year in BS that we can process
	 * returns $max_bs_year attribute if define
	 * else computes the maximum year in the $this->bs attribute, updates the $max_bs_year attribute and returns it's value
	 */
	protected function updateMaxValidYearBS()
	{
		if ($this->max_bs_year != null)
		{
			return $this->max_bs_year;
		}

		$max_year = 0;
		foreach($this->bs as $year=>$month_array)
		{
			if ($year > $max_year)
			{
				$max_year = $year;
			}
		}
		$this->max_bs_year = (int)$max_year;
		return $this->max_bs_year;
	}

	/**
	 * Get max valid BS year
	 */
	protected function getMaxValidDateBS()
	{
		$max_bs_year = $this->updateMaxValidYearBS();
		$max_bs_month = 12;
		$max_bs_day = $this->bs[$max_bs_year][$max_bs_month - 1];
		return $max_bs_month.'/'.$max_bs_day.'/'.$max_bs_year;
	}

	/**
	 * Update max valid AD date 
	 */
	protected function updateMaxValidDateAD()
	{
		$max_ad_date = $this->bs2ad($this->slashDateToDashDate($this->getMaxValidDateBS()));
		$max_ad_date = explode('-',$max_ad_date);

		$this->max_ad_date = array(
									'year' => (int)$max_ad_date[0],
									'month' => (int)$max_ad_date[1],
									'day' => (int)$max_ad_date[2]
							);
	}

	/**
	 * Find if the given AD date is valid and doesn't exceed our range
	 * Date format is mm/dd/yyyy
	 */
	protected function checkAdDate($date)
	{
		//TODO: even when the date is within our range, check if it's valid (e.g. Feb 30 etc)

		$x=explode('/',$date);
		$year=(int)($x[2]);
		$day=(int)($x[1]);
		$month=(int)($x[0]);

		if(!checkdate ($month , $day , $year ))
		{
			return false;
		}
		elseif($year > $this->max_ad_date['year'])
		{
			return false;
		}
		elseif($year == $this->max_ad_date['year'])
		{
			if ($month > $this->max_ad_date['month'])
			{
				return false;
			}
			elseif ($month == $this->max_ad_date['month'])
			{
				if ($day > $this->max_ad_date['day'])
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Find if the given BS date is valid and doesn't exceed our range
	 * Date format is mm/dd/yyyy
	 */
	protected function checkBsDate($date)
	{
		//Since we have information about all the month of the year, we don't have to check month the way we did in AD
		//just checking if the lies within our range will suffice

		$x=explode('/',$date);
		$year=(int)($x[2]);
		$day=(int)($x[1]);
		$month=(int)($x[0]);

		if( 	
				($day > $this->bs[$year][$month-1]) ||
				($year > $this->max_bs_year)
			)
		{
			return false;
		}

		return true;
	}

	public static function convertDay($date, $format, $ad2bsOrbs2ad)
	{
		if($ad2bsOrbs2ad == 'ad2bs')
		{
			$ad_date = Carbon::createFromFormat($format, $date)->format('Y-m-d');
			$bs_date = (new DateConverter)->ad2bs($ad_date);
			return $bs_date;
		}
	}

	//format Y-m-d
	public static function getYearMonthDayOfBs($bs_date)
	{
		return explode('-', $bs_date);
	}

	/**
	 * Converts from year in yyyy-mm-dd format to mm/dd/yyyy
	 */
	protected function dashDateToSlashDate($date)
	{
		$date_array = explode('-',$date);
		return $date_array[1].'/'.$date_array[2].'/'.$date_array[0];
	}

	/**
	 * Converts from year in mm/dd/yyyy format to yyyy-mm-dd 
	 */
	protected function slashDateToDashDate($date)
	{
		$date_array = explode('/',$date);
		return $date_array[2].'-'.$date_array[0].'-'.$date_array[1];
	}
}
?>