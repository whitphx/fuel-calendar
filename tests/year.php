<?php

/**
 * @group Package
 * @group Calendar
 * @group Year
 */
class Test_Year extends \TestCase {
	public function test_forge() {
		$year = \Calendar\Year::forge(2014);
		$this->assertEquals(2014, $year->year());
	}
	
	/**
	 * @dataProvider begin_and_end_provider
	 */
	public function test_begin_and_end($y, $begin, $end) {
		$year = \Calendar\Year::forge($y);
		$this->assertEquals($begin, $year->begin()->format('Y-m-d'));
		$this->assertEquals($end, $year->end()->format('Y-m-d'));
	}
	
	public function begin_and_end_provider() {
		return array(
			array(2000, '2000-01-01', '2000-12-31'),
			array(2004, '2004-01-01', '2004-12-31'),
			array(2010, '2010-01-01', '2010-12-31'),
			array(2014, '2014-01-01', '2014-12-31'),
			array(2100, '2100-01-01', '2100-12-31'),
		);
	}
	
	/**
	 * @dataProvider days_offset_provider
	 */
	public function test_offset($y, $offset, $day)
	{
		$year = \Calendar\Year::forge($y);
		$this->assertEquals($day, $year->day($offset)->format('Y-m-d'));
	}
	
	public function days_offset_provider()
	{
		return array(
			array(2015, 1, '2015-01-01'),
			array(2015, 10, '2015-01-10'),
			array(2015, 100, '2015-04-10'),
		);
	}
	
	/**
	 * @dataProvider days_provider
	 */
	public function test_days($y, $days) {
		$year = \Calendar\Year::forge($y);
		$this->assertEquals($days, count($year->days()));
		
		$datetime = new \DateTime();
		$datetime->setDate($y, 1, 1);
		$interval_1d = new \DateInterval('P1D');
		foreach ($year->days() as $day)
		{
			$this->assertEquals($datetime->format('Y-m-d'), $day->format('Y-m-d'));
			$datetime->add($interval_1d);
		}
	}
	
	public function days_provider() {
		return array(
			array(2000, 366),
			array(2004, 366),
			array(2010, 365),
			array(2100, 365),
		);
	}
}