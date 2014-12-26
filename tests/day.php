<?php

/**
 * @group Package
 * @group Calendar
 */
class Test_Day extends \TestCase {
	public function test_forge() {
		$day = \Calendar\Day::forge(2014, 1, 2);
		$this->assertEquals(2014, $day->year());
		$this->assertEquals(1, $day->month());
		$this->assertEquals(2, $day->day());
	}
	
	/**
	 * @dataProvider add_provider
	 */
	public function test_add($prev, $next, $offset) {
		$parse = date_parse($prev);
		$day = \Calendar\Day::forge($parse['year'], $parse['month'], $parse['day']);
		$this->assertEquals($next, $day->add($offset)->format('Y-m-d'));
		
		$parse = date_parse($next);
		$day = \Calendar\Day::forge($parse['year'], $parse['month'], $parse['day']);
		$this->assertEquals($prev, $day->add(-$offset)->format('Y-m-d'));
	}
	
	public function add_provider() {
		return array(
			array('2013-12-31', '2014-01-01', 1),
			array('2014-01-31', '2014-02-01', 1),
			array('2014-02-28', '2014-03-01', 1),
		);
	}
}