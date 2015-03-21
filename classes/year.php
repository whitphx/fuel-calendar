<?php

namespace Calendar;

class Year {
	protected $_datetime;
	protected $_days_in_year;
	protected $_days = null; // cache
	
	public function __construct($year) {
		$this->_datetime = new \DateTime();
		$this->_datetime->setDate($year, 1, 1);
		$this->_datetime->setTime(0, 0, 0);
		
		$this->_days_in_year = checkdate(2, 29, $year) ? 366 : 365;
	}
	
	public static function forge($year = null) {
		is_null($year) and $year = (int) date('Y');
		return new static($year);
	}
	
	public function year() {
		return (int) $this->_datetime->format('Y');
	}
	
	public function begin() {
		return $this->day(1);
	}
	
	public function end() {
		return $this->day($this->_days_in_year);
	}
	
	public function day($offset) {
		if ( ! is_int($offset)) {
			throw new \OutOfBoundsException('Offset must be integer.');
		}
		
		$offset--;
		
		$day = clone $this->_datetime;
		if ($offset > 0) {
			$day->add(new \DateInterval('P'.$offset.'D'));
		} else if ($offset < 0) {
			$day->sub(new \DateInterval('P'.(-$offset).'D'));
		}
		
		return \Day::forge($day);
	}
	
	public function days() {
		if ( ! $this->_days) {
			$this->_days = array();
			foreach (range(1, $this->_days_in_year) as $offset) {
				$this->_days[] = $this->day($offset);
			}
		}
		
		return $this->_days;
	}
	
	public function max_week()
	{
		return date('W', mktime(0, 0, 0, 12, 31, $this->year())) == 53 ? 53 : 52;
	}
}