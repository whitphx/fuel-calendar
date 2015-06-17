<?php

namespace Calendar;

class Month {
	protected $_firstday;
	protected $_datetime;
	protected $_days_in_month;
	protected $_days = null; // cache

	// Weekと同じ．いずれ共通親クラスにに抜き出したい
	const WEEK   = 604800;
	const DAY    = 86400;

	public function __construct($year, $month, $firstday) {
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$this->_firstday = min(array($firstday, $days_in_month));
		
		$this->_datetime = new \DateTime();
		$this->_datetime->setDate($year, $month, $this->_firstday);
		$this->_datetime->setTime(0, 0, 0);
		
		if ($this->_firstday >= 28)
		{
			/**
			 * 例えばfirstday=31とした時，1月は1/31始まり．
			 * その翌月は28日しか存在しないので，月末を繰り上げて2/28日始まりとなる．．
			 * このとき，1月のdays_in_month=31のままだと1月は1/31~3/2となって2月に被ってしまう．
			 * これを補正する．
			 * この問題は翌月の日数が少なく月末の繰り上げが起きる時に問題となる．
			 */
			list ($next_y, $next_m) = static::_add($year, $month, 1);
			$days_in_next_month = cal_days_in_month(CAL_GREGORIAN, $next_m, $next_y);
			$next_begin_day = min(array($this->_firstday, $days_in_next_month));
			$this->_days_in_month = $days_in_month - ($this->_firstday - $next_begin_day);
		}
		else
		{
			$this->_days_in_month = $days_in_month;
		}
	}
	
	public static function forge($year = null, $month = null, $firstday = 1) {
		is_null($year) and $year = (int) date('Y');
		is_null($month) and $month = (int) date('n');
		return new static($year, $month, $firstday);
	}
	
	public function year() {
		return (int) $this->_datetime->format('Y');
	}
	
	public function month() {
		return (int) $this->_datetime->format('n');
	}
	
	/**
	 * 
	 * @return Day
	 */
	public function begin() {
		return $this->day(1);
	}
	
	/**
	 * 
	 * @return Day
	 */
	public function end() {
		return $this->day($this->_days_in_month);
	}
	
	/**
	 * 
	 * @return static
	 */
	public function next()
	{
		return $this->add(1);
	}
	
	/**
	 * 
	 * @return static
	 */
	public function prev()
	{
		return $this->add(-1);
	}

	/**
	 * 
	 * @param type $addition
	 * @return static
	 * @throws \OutOfBoundsException
	 */
	public function add($addition, $firstday = null)
	{
		if ( !is_int($addition)) {
			throw new \OutOfBoundsException('the argument must be integer.');
		}
		
		is_null($firstday) and $firstday = $this->_firstday;
		
		if ($addition === 0)
		{
			return static::forge($this->year(), $this->month(), $firstday);
		}
		
		list($year, $month) = static::_add($this->year(), $this->month(), $addition);
		
		return static::forge($year, $month, $firstday);
	}
	
	private static function _add($year, $month, $addition)
	{
		$month_tmp = $month + $addition;
		$_y = $year + floor( ($month_tmp - 1) / 12);
		$_m = ($month_tmp - 1) % 12 + 1;
		$_m <= 0 and $_m += 12;
		
		return array($_y, $_m);
	}
	
	/**
	 * 
	 * @param type $offset
	 * @return Day
	 * @throws \OutOfBoundsException
	 */
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