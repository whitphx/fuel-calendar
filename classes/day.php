<?php

namespace Calendar;

class Day {
	protected $_datetime;

	// Weekと同じ．いずれ共通親クラスにに抜き出したい
	const WEEK   = 604800;
	const DAY    = 86400;
	
	public function __construct($year, $month, $day) {
		$this->_datetime = new \DateTime();
		$this->_datetime->setDate($year, $month, $day);
		$this->_datetime->setTime(0, 0, 0);
	}
	
	public static function forge($year = null, $month = null, $day = null) {
		is_null($year) and $year = (int) date('Y');
		is_null($month) and $month = (int) date('n');
		is_null($day) and $day = (int) date('j');
		
		return new static($year, $month, $day);
	}
	
	public function format($format) {
		return $this->_datetime->format($format);
	}
	
	public function year() {
		return (int) $this->_datetime->format('Y');
	}
	
	public function month() {
		return (int) $this->_datetime->format('n');
	}
	
	public function day() {
		return (int) $this->_datetime->format('j');
	}
	
	public function add($addition) {
		if ( !is_int($addition)) {
			throw new \OutOfBoundsException('the argument must be integer.');
		}
		
		$added_date = clone $this->_datetime;
		if ($addition > 0) {
			$added_date->add(new \DateInterval('P'.$addition.'D'));
		} else if ($addition < 0) {
			$added_date->sub(new \DateInterval('P'.(-$addition).'D'));
		} else {
			return $added_date;
		}
		
		$year = (int) $added_date->format('Y');
		$month = (int) $added_date->format('n');
		$day = (int) $added_date->format('j');
		
		return static::forge($year, $month, $day);
	}
	
	public function getTimestamp() {
		return $this->_datetime->getTimestamp();
	}
}