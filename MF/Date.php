<?php
/**
* MF_Date
* UTF8 Encoded version
*
* @version 2.5.1 2009-01-15
* - static keyword
*
* @version 2.5.0 2008-09-09
* - changed internal values
* - added addXYZ functions
*
* @version 2.0.1 2007-12-12
* - added age function
*
* @version 2.0.0 2007-06-13
*/

namespace MF;

class Date {
	private $year		= 0;
	private $month		= 0;
	private $day		= 0;
	private $hours		= 0;
	private $minutes	= 0;
	private $seconds	= 0;
	
	/**
	 * Constructor
	 *
	 * @param $date		String			Date or Datetime
	 */
	public function __construct ($date = '') {
		if (strlen($date) > 0) {
			if (strlen($date) == 19) {
				$this->setDateFromDatetime($date);
			}
			else {
				$this->setDateFromDate($date);
			}
		}
		else {
			$this->setDateFromDatetime(date('Y-m-d H:i:s'));
		}
	}
	
/**************************************************
* DATE COMPOSANTS (YEAR, MONTH...)
**************************************************/
	
	/**
	 * Set year
	 *
	 * @param $year		Int, String		Year
	 */
	public function setYear ($year = 1970) {
		$this->year = 0 + $year;
	}
	
	/**
	 * Get year
	 * 
	 * @return String 
	 */
	public function getYear () {
		return (String::sizedNumber($this->year, 4));
	}
	
	/**
	 * Add some years
	 *
	 * @param $years	Int				Number of years to add
	 */
	public function addYears ($years) {
		$this->year += $years;
	}
	
	/**
	 * Set month
	 *
	 * @param $month	Int, String		Month (1-12)
	 */
	public function setMonth ($month = 1) {
		$this->month = 0 + $month;
	}
	
	/**
	 * Get month
	 *
	 * @return String
	 */
	public function getMonth () {
		return (String::sizedNumber($this->month, 2));
	}
	
	/**
	 * Add some months
	 *
	 * @param $months	Int				Number of months to add
	 */
	public function addMonths ($months) {
		$years	= floor(($this->month - 1 + $months) / 12);
		$this->setMonth(($this->month - 1 + $months) % 12 + 1);
		$this->addYears($years);
	}
	
	/**
	 * Set day
	 *
	 * @param $day		Int, String		Day
	 */
	public function setDay ($day = 1) {
		$this->day = 0 + $day;
	}
	
	/**
	 * Get day
	 *
	 * @return String
	 */
	public function getDay () {
		return (String::sizedNumber($this->day, 2));
	}
	
	/**
	 * Add some days
	 *
	 * @param $days		Int				Number of days to add
	 */
	public function addDays ($days) {
		$this->addTime($days * 24 * 60 * 60);
	}
	
	/**
	 * Set hours
	 *
	 * @param $hour		Int, String		Hours
	 */
	public function setHours ($hour = 0) {
		$this->hours = 0 + $hour;
	}
	
	/**
	 * Get Hours
	 *
	 * @return String
	 */
	public function getHours () {
		return (String::sizedNumber($this->hours, 2));
	}
	
	/**
	 * Add some Hours
	 *
	 * @param $hours	Int				Number of hours to add
	 */
	public function addHours ($hours) {
		$this->addTime($hours * 60 * 60);
	}
	
	/**
	 * Set minutes
	 *
	 * @param $minute	Int, String		Minutes
	 */
	public function setMinutes ($minute = 0) {
		$this->minutes = 0 + $minute;
	}
	
	/**
	 * Get minutes
	 *
	 * @return String
	 */
	public function getMinutes () {
		return (String::sizedNumber($this->minutes, 2));
	}
	
	/**
	 * Add some Minutes
	 *
	 * @param $minutes	Int				Number of minutes to add
	 */
	public function addMinutes ($minutes) {
		$this->addTime($minutes * 60);
	}
	
	/**
	 * Set seconds
	 *
	 * @param $second	Int, String		Seconds
	 */
	public function setSeconds ($second = 0) {
		$this->seconds = 0 + $second;
	}
	
	/**
	 * Get seconds
	 * 
	 * @return String
	 */
	public function getSeconds () {
		return (String::sizedNumber($this->seconds, 2));
	}
	
	/**
	 * Add some Seconds
	 *
	 * @param $seconds	Int				Number of seconds to add
	 */
	public function addSeconds ($seconds) {
		$this->addTime($seconds);
	}
	
/**************************************************
* SET DATE
**************************************************/
	
	/**
	 * Set date from SQL datetime.
	 *
	 * @param $date		String			Datetime
	 */
	public function setDateFromDatetime ($date) {
		$this->setYear(substr($date, 0, 4));
		$this->setMonth(substr($date, 5, 2));
		$this->setDay(substr($date, 8, 2));
		$this->setHours(substr($date, 11, 2));
		$this->setMinutes(substr($date, 14, 2));
		$this->setSeconds(substr($date, 17, 2));
	}
	
	/**
	 * Set date from SQL date.
	 *
	 * @param $date		String			Date
	 */
	public function setDateFromDate ($date) {
		$this->setYear(substr($date, 0, 4));
		$this->setMonth(substr($date, 5, 2));
		$this->setDay(substr($date, 8, 2));
		$this->setHours();
		$this->setMinutes();
		$this->setSeconds();
	}
	
	/**
	 * Set date from English MM/DD/YYYY date.
	 *
	 * @param $date		String			Date
	 */
	public function setDateFromEnglishDate ($date) {
		$this->setYear(substr($date, 6, 4));
		$this->setMonth(substr($date, 0, 2));
		$this->setDay(substr($date, 3, 2));
		$this->setHours();
		$this->setMinutes();
		$this->setSeconds();
	}
	
	/**
	 * Set date from French DD/MM/YYYY date.
	 *
	 * @param $date		String			Date
	 */
	public function setDateFromFrenchDate ($date) {
		$this->setYear(substr($date, 6, 4));
		$this->setMonth(substr($date, 3, 2));
		$this->setDay(substr($date, 0, 2));
		$this->setHours();
		$this->setMinutes();
		$this->setSeconds();
	}
	
	/**
	 * Set date from timestamp
	 * 
	 * @param $time		Int
	 */
	public function setDateFromTimestamp ($time) {
		$this->setDateFromDatetime(date('Y-m-d H:i:s', $time));
	}
	
	/**
	 * Add seconds to date
	 * 
	 * @param $time		Int
	 */
	public function addTime ($time) {
		$this->setDateFromTimestamp($this->toTimestamp() + $time);
	}
	
/**************************************************
* GET
**************************************************/
	
	/**
	 * Return timestamp
	 * 
	 * @return Int
	 */
	public function toTimestamp () {
		if ($this->year < 1970) {
			return (0);
		}
		else {
			return (mktime(	$this->hours, 
							$this->minutes,
							$this->seconds,
							$this->month,
							$this->day,
							$this->year));
		}
	}
	
	/**
	 * Return SQL Date (ie: 2006-08-26)
	 * 
	 * @return String
	 */
	public function toSQLDate () {
		$date = $this->getYear().'-'.$this->getMonth().'-'.$this->getDay();
		
		return ($date);
	}
	
	/**
	 * Return SQL Datetime (ie: 2006-08-26 22:23:24)
	 * 
	 * @return String
	 */
	public function toSQLDatetime () {
		$date = $this->getYear().'-'.$this->getMonth().'-'.$this->getDay().' '.$this->getHours().':'.$this->getMinutes().':'.$this->getSeconds();
		
		return ($date);
	}
	
	/**
	 * Return french full date (ie: mercredi 26 août 2006) 
	 * 
	 * @return String
	 */
	public function toDate () {
		$week_day = self::dayName(date('w', mktime(0, 0, 0, $this->month, $this->day, $this->year)));
		$date = $week_day.' '.$this->toShortDate();
		
		return ($date);
	}
	
	/**
	 * Return french date (ie: 26 août 2006)
	 * 
	 * @return String
	 */
	public function toShortDate () {
		$date = $this->getDay().' '.self::monthName($this->getMonth()).' '.$this->getYear();
		
		return ($date);
	}
	
	/**
	 * Return Short date in french format (ie: DD/MM/YYYY)
	 * 
	 * @return String
	 */
	public function toFrenchDate ($empty_as_null = false) {
		$date = $this->getDay().'/'.$this->getMonth().'/'.$this->getYear();
		
		if ($empty_as_null && ($date == '00/00/0000')) {
			return ('');
		}
		else {
			return ($date);
		}
	}
	
	/**
	 * Return Short date in english format (ie: MM/DD/YYYY)
	 * 
	 * @return String
	 */
	public function toEnglishDate ($empty_as_null = false) {
		$date = $this->getMonth().'/'.$this->getDay().'/'.$this->getYear();
		
		if ($empty_as_null && ($date == '00/00/0000')) {
			return ('');
		}
		else {
			return ($date);
		}
	}
	
/**************************************************
* STATIC METHODS
**************************************************/
	
	/**
	 * Return the number of days of a given month
	 * 
	 * @param $month	Int	
	 * @param $year		Int
	 * 
	 * @return Int
	 */
	public static function monthLastDay ($month, $year) {
		$d = 31;
		while (!checkdate($month, $d, $year)) {
			$d--;
		}
		return ($d);
	}
	
	/**
	 * Return french day name
	 * 
	 * @param $day		Int			Day number (1 = Monday, 0 or 7 = Sunday)
	 * 
	 * @return String
	 */
	public static function dayName ($day) {
		$dayName = array (
			0 => "Dimanche",
			1 => "Lundi",
			2 => "Mardi",
			3 => "Mercredi",
			4 => "Jeudi",
			5 => "Vendredi",
			6 => "Samedi"
		);
		
		return ($dayName[(0 + $day) % 7]);
	}
	
	/**
	 * Return french month name
	 * 
	 * @param $month	Int			Month number (1 = January, 12 = December)
	 * 
	 * @return String
	 */
	public static function monthName ($month) {
		$monthName = array (
			1 => "Janvier",
			2 => "Février",
			3 => "Mars",
			4 => "Avril",
			5 => "Mai",
			6 => "Juin",
			7 => "Juillet",
			8 => "Août",
			9 => "Septembre",
			10 => "Octobre",
			11 => "Novembre",
			12 => "Décembre"
		);
		
		return ($monthName[0 + $month]);
	}
	
	/**
	 * Return month short name
	 * 
	 * @param $month	Int			Month number (1 = January, 12 = December)
	 * 
	 * @return String
	 */
	public static function monthNameShort ($month) {
		$monthNameShort = array (
			1 => "Jan",
			2 => "Fév",
			3 => "Mar",
			4 => "Avr",
			5 => "Mai",
			6 => "Juin",
			7 => "Juil",
			8 => "Aoû",
			9 => "Sep",
			10 => "Oct",
			11 => "Nov",
			12 => "Déc"
		);
		
		return ($monthNameShort[0 + $month]);
	}
	
	/**
	 * Return the age from a given birthdate
	 *
	 * @param $date_birth	String	Date in SQL Format (ie: 1978-02-11)
	 */
	public static function age ($date_birth) {
		list($year_birth, $month_birth, $day_birth) = explode('-', $date_birth);
		
		$month	= date('n');
		$day	= date('j');
		$year	= date('Y');
		
		$years = $year - $year_birth;
		
  		if (($month < $month_birth) || (($month == $month_birth) && ($day < $day_birth))) {
        	$years--;
      	}
      	
      	return ($years);
  	}
}
