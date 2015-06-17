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
	
	/**
	 * @dataProvider add_provider
	 */
	public function test_add($y1, $m1, $y2, $m2, $offset)
	{
		$month = \Calendar\Month::forge($y1, $m1);
		$added_month = $month->add($offset);
		$this->assertEquals($y2, $added_month->year());
		$this->assertEquals($m2, $added_month->month());
		
		$month2 = \Calendar\Month::forge($y2, $m2);
		$subbed_month = $month2->add(-$offset);
		$this->assertEquals($y1, $subbed_month->year());
		$this->assertEquals($m1, $subbed_month->month());
	}
	
	public function add_provider()
	{
		return array(
			array(2010, 1, 2010, 2, 1),
			array(2010, 11, 2010, 12, 1),
			array(2010, 12, 2011, 1, 1),
			array(2010, 12, 2012, 12, 24),
			array(2010, 12, 2013, 2, 26),
		);
	}
	
	public function test_firstday_offset()
	{
		$month = \Calendar\Month::forge(2010, 10, 15);
		$this->assertEquals('2010-10-15', $month->begin()->format('Y-m-d'));
		$this->assertEquals('2010-11-14', $month->end()->format('Y-m-d'));
	}
	
	public function test_add_with_firstday_offset()
	{
		$m_2010_1 = \Calendar\Month::forge(2010, 1, 31);
		$this->assertEquals('2010-01-31', $m_2010_1->begin()->format('Y-m-d'));
		$this->assertEquals('2010-02-27', $m_2010_1->end()->format('Y-m-d'));
		
		$m_2010_2 = $m_2010_1->next();
		$this->assertEquals('2010-02-28', $m_2010_2->begin()->format('Y-m-d'));
		$this->assertEquals('2010-03-27', $m_2010_2->end()->format('Y-m-d'));
		
		$m_2010_3_after_2 = $m_2010_2->next();
		$this->assertEquals('2010-03-28', $m_2010_3_after_2->begin()->format('Y-m-d'));
		$this->assertEquals('2010-04-27', $m_2010_3_after_2->end()->format('Y-m-d'));
		
		$m_2010_3_after_1 = $m_2010_1->add(2);
		$this->assertEquals('2010-03-31', $m_2010_3_after_1->begin()->format('Y-m-d'));
		$this->assertEquals('2010-04-29', $m_2010_3_after_1->end()->format('Y-m-d'));
	}
}