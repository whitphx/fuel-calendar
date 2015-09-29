<?php

namespace Calendar;

class Week
{
	/**
	 * _canonical_datetimeが表現するDateのISO週番号に則った年
	 * @var int
	 */
	protected $_iso_year;

	/**
	 * _canonical_datetimeが表現するDateのISO週番号
	 * @var int
	 */
	protected $_iso_week;

	/**
	 * _canonical_datetimeと_firstdayの組み合わせによって決まる，実際にインスタンスが表現する週の初日のDate
	 * @var \DateTime
	 */
	private $_datetime;

	/**
	 * ISO 週番号の定義に則り，月曜日始まりでオフセットを考慮しない場合のDateを表現，
	 * @var \DateTime
	 */
	private $_canonical_datetime;

	/**
	 * インスタンスが表現する週の始まりの曜日．
	 * @var int
	 */
	private $_firstday;

	const WEEK   = 604800;
	const DAY    = 86400;

	/**
	 *
	 * @param int $year ISO year
	 * @param int $week ISO week
	 */
	public function __construct($year, $week, $day = 1)
	{
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

	/**
	 *
	 * @param int $year
	 * @param int $week
	 * @param int $day
	 * @return static
	 */
	public static function forge($year = null, $week = null, $day = 1)
	{
		is_null($year) and $year = (int) date('o');
		is_null($week) and $week = (int) date('W');

		return new static($year, $week, $day);
	}

	/**
	 *
	 * @param int $year year
	 * @param int $month month
	 * @param int $week week number of year
	 * @param int $day the first day's day number of week
	 * @return static
	 */
	public static function forge_by_week_of_month($year, $month, $week, $day = 1)
	{
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

	/**
	 * Create instance with date string specified
	 * @param string $date A string representing the date
	 * @param int $day The first day's day number of week
	 * @return static
	 */
	public static function forge_by_date($date = null, $day = 1)
	{
		$time = is_null($date) ? time() : strtotime($date);
		$time -= ($day - 1) * static::DAY;

		$year = (int) date('o', $time);
		$week = (int) date('W', $time);
		return new static($year, $week, $day);
	}

	/**
	 * Create instance with UNIX timestamp specified
	 * @param int $time UNIX timestamp
	 * @param int $day The first day's day number of week
	 */
	public static function forge_by_time($time, $day = 1)
	{
		$year = (int) date('o', $time);
		$week = (int) date('W', $time);
		return new static($year, $week, $day);
	}

	/**
	 *
	 * @param type $addition
	 * @return static
	 * @throws \OutOfBoundsException
	 */
	public function add($addition)
	{
		if ( !is_int($addition)) {
			throw new \OutOfBoundsException('the argument must be integer.');
		}

		$added_date = clone $this->_canonical_datetime;
		if ($addition > 0) {
			$added_date->add(new \DateInterval('P'.$addition.'W'));
		} else if ($addition < 0) {
			$added_date->sub(new \DateInterval('P'.(-$addition).'W'));
		} else {
			return static::forge($this->iso_year(), $this->iso_week(), $this->_firstday);
		}

		$added_iso_year = (int) $added_date->format('o');
		$added_iso_week = (int) $added_date->format('W');

		return static::forge($added_iso_year, $added_iso_week, $this->_firstday);
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
	 * @param type $duplicate
	 * @return Array
	 */
	public function of_month($duplicate = true)
	{
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

	/**
	 *
	 * @param type $offset
	 * @return Day
	 * @throws \OutOfBoundsException
	 */
	public function day($offset)
	{
		if ( ! is_int($offset)) {
			throw new \OutOfBoundsException('Offset must be integer.');
		}

		$day = clone $this->_datetime;
		if ($offset > 0) {
			$day->add(new \DateInterval('P'.$offset.'D'));
		} else if ($offset < 0) {
			$day->sub(new \DateInterval('P'.(-$offset).'D'));
		}

		return \Day::forge($day);
	}

	/**
	 *
	 * @return int
	 */
	public function iso_year()
	{
		return $this->_iso_year;
	}

	/**
	 *
	 * @return int
	 */
	public function iso_week()
	{
		return $this->_iso_week;
	}

	/**
	 *
	 * @param type $year
	 * @return int
	 */
	public static function max_week($year)
	{
		return \Calendar\Year::forge($year)->max_week();
	}

	/**
	 *
	 * @return Array
	 */
	public function days()
	{
		$res = array();
		foreach (range(0, 6) as $offset) {
			$res[] = $this->day($offset);
		}
		return $res;
	}

	/**
	 *
	 * @return Day
	 */
	public function begin()
	{
		return $this->day(0);
	}

	/**
	 *
	 * @return Day
	 */
	public function end()
	{
		return $this->day(6);
	}

	/**
	 *
	 * @return int
	 */
	public function getTimestamp()
	{
		return $this->get_timestamp();
	}

	/**
	 *
	 * @return int
	 */
	public function get_timestamp()
	{
		return $this->_datetime->getTimestamp();
	}

	public function get_first_day()
	{
		return $this->_firstday;
	}
}
