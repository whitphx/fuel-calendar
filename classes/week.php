<?php

namespace Calendar;

class Week {
	protected $_iso_year;           // _canonical_datetimeが表現するDateのISO週番号に則った年
	protected $_iso_week;           // _canonical_datetimeが表現するDateのISO週番号
	private $_datetime;             // _canonical_datetimeと_firstdayの組み合わせによって決まる，実際にインスタンスが表現する週の初日のDate
	private $_canonical_datetime;   // ISO 週番号の定義に則り，月曜日始まりでオフセットを考慮しない場合のDateを表現，
	private $_firstday;             // インスタンスが表現する週の始まりの曜日．

	const WEEK   = 604800;
	const DAY    = 86400;

	/**
	 * 
	 * @param int $year ISO
	 * @param int $week ISO
	 */
	public function __construct($year, $week, $day = 1) {        
		$this->_datetime = new \DateTime();
		$this->_datetime->setISODate($year, $week, $day);
		$this->_datetime->setTime(0, 0, 0);
		
		$diff_offset = $day - 1;
		$this->_canonical_datetime = clone $this->_datetime;
		if ($diff_offset > 0) {
			$this->_canonical_datetime->sub(new \DateInterval('P'.$diff_offset.'D'));
		} else if ($diff_offset < 0) {
			$this->_canonical_datetime->add(new \DateInterval('P'.(-$diff_offset).'D'));
		}
		
		$this->_iso_year = (int) $this->_canonical_datetime->format('o');
		$this->_iso_week = (int) $this->_canonical_datetime->format('W');
		$this->_firstday = $day;
	}
	
	public static function forge($year, $week, $day = 1) {
		return new static($year, $week, $day);
	}
	
	public static function forge_by_week_of_month($year, $month, $week, $day = 1) {
		$timestamp_day1 = mktime(0, 0, 0, $month, 1, $year);
		$iso_year_day1 = date('o', $timestamp_day1);
		$iso_week_day1 = date('W', $timestamp_day1);
		
		$iso_year = $iso_year_day1;
		$iso_week = $iso_week_day1 + $week - 1;
		if ($iso_week >= 52) {
			$iso_week -= static::max_week($iso_year_day1);
			$iso_year++;
		}
		
		return new static($iso_year, $iso_week, $day);
	}
	
	public static function forge_by_date($date = null, $day = 1) {
		$time = is_null($date) ? time() : strtotime($date);
		$time -= ($day - 1) * static::DAY;
		
		$year = (int) date('o', $time);
		$week = (int) date('W', $time);
		return new static($year, $week, $day);
	}
	
	public function add($addition) {
		if ( !is_int($addition)) {
			throw new \OutOfBoundsException('the argument must be integer.');
		}
		
		$added_date = clone $this->_canonical_datetime;
		if ($addition > 0) {
			$added_date->add(new \DateInterval('P'.$addition.'W'));
		} else if ($addition < 0) {
			$added_date->sub(new \DateInterval('P'.(-$addition).'W'));
		} else {
			return $added_date;
		}
		
		$added_iso_year = (int) $added_date->format('o');
		$added_iso_week = (int) $added_date->format('W');
		
		return static::forge($added_iso_year, $added_iso_week, $this->_firstday);
	}
	
	public function next() {
		return $this->add(1);
	}
	
	public function prev() {
		return $this->add(-1);
	}
	
	public function of_month($duplicate = true) {
		if ($duplicate) {
			$lastday = clone $this->_datetime;
			$lastday->add(new \DateInterval('P6D'));
			$y = (int) $lastday->format('Y');
			$m = (int) $lastday->format('n');
		} else {
			$y = (int) $this->_datetime->format('Y');
			$m = (int) $this->_datetime->format('n');
		}
		
		$timestamp_day1 = mktime(0, 0, 0, $m, 1, $y);
		$y_day1 = (int) date('o', $timestamp_day1);
		$w_day1 = (int) date('W', $timestamp_day1);
		if ($y_day1 < $this->_iso_year) {
			$w_day1 -= static::max_week($y_day1);
		}
		
		$w = $this->_iso_week - $w_day1 + 1;
		
		return array($y, $m, $w);
	}
	
	public function day($offset) {
		if ( ! is_int($offset)) {
			throw new \OutOfBoundsException('Offset must be integer.');
		}
		
		$day = clone $this->_datetime;
		if ($offset > 0) {
			$day->add(new \DateInterval('P'.$offset.'D'));
		} else if ($offset < 0) {
			$day->sub(new \DateInterval('P'.(-$offset).'D'));
		}
		
		return $day;
	}
	
	public function iso_year() {
		return $this->_iso_year;
	}
	
	public function iso_week() {
		return $this->_iso_week;
	}
	
	public static function max_week($year) {
		return date('W', mktime(0, 0, 0, 12, 31, $year)) == 53 ? 53 : 52;
	}
	
	public function days() {
		$res = array();
		foreach (range(0, 6) as $offset) {
			$res[] = $this->day($offset);
		}
		return $res;
	}
}