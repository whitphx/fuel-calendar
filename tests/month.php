<?php

/**
 * @group Package
 * @group Calendar
 * @group Month
 */
class Test_Month extends \TestCase {
	public function test_forge() {
		$month = \Calendar\Month::forge(2014, 1);
		$this->assertEquals(2014, $month->year());
		$this->assertEquals(1, $month->month());
	}
	
	/**
	 * @dataProvider begin_and_end_provider
	 */
	public function test_begin_and_end($y, $m, $begin, $end) {
		$month = \Calendar\Month::forge($y, $m);
		$this->assertEquals($begin, $month->begin()->format('Y-m-d'));
		$this->assertEquals($end, $month->end()->format('Y-m-d'));
	}
	
	public function begin_and_end_provider() {
		return array(
			array(2012, 1, '2012-01-01', '2012-01-31'),
			array(2012, 2, '2012-02-01', '2012-02-29'),
			array(2012, 3, '2012-03-01', '2012-03-31'),
			array(2012, 4, '2012-04-01', '2012-04-30'),
			array(2014, 1, '2014-01-01', '2014-01-31'),
			array(2014, 2, '2014-02-01', '2014-02-28'),
			array(2014, 3, '2014-03-01', '2014-03-31'),
			array(2014, 4, '2014-04-01', '2014-04-30'),
		);
	}
	
	/**
	 * @dataProvider days_provider
	 */
	public function test_days($y, $m, $days) {
		$month = \Calendar\Month::forge($y, $m);
		$this->assertEquals($days, count($month->days()));
		
		$datetime = new \DateTime();
		$datetime->setDate($y, $m, 1);
		$datetime->setTime(0, 0, 0);
		foreach ($month->days() as $day) {
			$this->assertEquals($datetime->format('Y-m-d'), $day->format('Y-m-d'));
			$datetime->add(new \DateInterval('P1D'));
		}
	}
	
	public function days_provider() {
		return array(
			array(2012, 1, 31),
			array(2012, 2, 29),
			array(2012, 3, 31),
			array(2012, 4, 30),
			array(2014, 1, 31),
			array(2014, 2, 28),
			array(2014, 3, 31),
			array(2014, 4, 30),
		);
	}
}