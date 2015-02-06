<?php

namespace Calendar;

class Month {
	protected $_datetime;
	protected $_days_in_month;
	protected $_days = null; // cache

	// Weekと同じ．いずれ共通親クラスにに抜き出したい
	const WEEK   = 604800;
	const DAY    = 86400;

	public function __construct($year, $month) {
		$this->_datetime = new \DateTime();
		$this->_datetime->setDate($year, $month, 1);
		$this->_datetime->setTime(0, 0, 0);
		
		$this->_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}
	
	public static function forge($year = null, $month = null) {
		is_null($year) and $year = (int) date('Y');
		is_null($month) and $month = (int) date('n');
		return new static($year, $month);
	}
	
	public function year() {
		return (int) $this->_datetime->format('Y');
	}
	
	public function month() {
		return (int) $this->_datetime->format('n');
	}
	
	public function begin() {
		return $this->day(1);
	}
	
	public function end() {
		return $this->day($this->_days_in_month);
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
			foreach (range(1, $this->_days_in_month) as $offset) {
				$this->_days[] = $this->day($offset);
			}
		}
		
		return $this->_days;
	}
}