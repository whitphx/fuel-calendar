<?php

/**
 * @group Package
 * @group Calendar
 */
class Test_Week extends \TestCase {
	/**
	 * @dataProvider week_incremental_provider
	 */
	public function test_week_increment($year, $week, $y, $w) {
		$week = \Calendar\Week::forge($year, $week);
		$nextweek = $week->next();
		$this->assertEquals($y, $nextweek->iso_year());
		$this->assertEquals($w, $nextweek->iso_week());
	}
	
	public function week_incremental_provider() {
		return array(
			array(2014, 1, 2014, 2),
			array(2013, 52, 2014, 1),
			array(2012, 52, 2013, 1),
			array(2011, 52, 2012, 1),
			array(2004, 52, 2004, 53),
			array(2004, 53, 2005, 1),
		);
	}
	
	/**
	 * @dataProvider week_incremental_provider
	 */
	public function test_week_decrement($y, $w, $year, $week) {
		$week = \Calendar\Week::forge($year, $week);
		$prevweek = $week->prev();
		$this->assertEquals($y, $prevweek->iso_year());
		$this->assertEquals($w, $prevweek->iso_week());
	}
	
	/**
	 * @dataProvider week_add_provider
	 */
	public function test_week_add($_year, $_week, $y, $w, $add) {
		foreach (range(0, 6) as $day) {
			$week = \Calendar\Week::forge($_year, $_week, $day);
			$added = $week->add($add);
			$this->assertEquals($y, $added->iso_year());
			$this->assertEquals($w, $added->iso_week());
		}
	}
	
	/**
	 * @dataProvider week_add_provider
	 */
	public function test_week_sub($y, $w, $year, $week, $add) {
		$week = \Calendar\Week::forge($year, $week);
		$added = $week->add(-$add);
		$this->assertEquals($y, $added->iso_year());
		$this->assertEquals($w, $added->iso_week());
	}
	
	public function week_add_provider() {
		return array(
			array(2014, 1, 2014, 3, 2),
			array(2013, 51, 2014, 1, 2),
			array(2013, 52, 2014, 2, 2),
			array(2012, 51, 2013, 1, 2),
			array(2012, 52, 2013, 2, 2),
			array(2004, 51, 2004, 53, 2),
			array(2004, 52, 2005, 1, 2),
			array(2004, 53, 2005, 2, 2),
		);
	}
	
	/**
	 * @dataProvider week_of_year_and_month_provider
	 */
	public function test_week_of_year_to_week_of_month($year_iso, $week_iso, $year, $month, $week_of_month, $solve_duplicate = false, $day_of_week = 1){
		$week = \Calendar\Week::forge($year_iso, $week_iso, $day_of_week);
		list($y, $m, $w) = $week->of_month($solve_duplicate);
		
		$this->assertInternalType('int', $y);
		$this->assertInternalType('int', $m);
		$this->assertInternalType('int', $w);
		$this->assertEquals($year, $y);
		$this->assertEquals($month, $m);
		$this->assertEquals($week_of_month, $w);
	}
	

	/**
	 * @dataProvider week_of_year_and_month_provider
	 */
	public function test_week_of_month_to_week_of_year_iso($year_iso, $week_of_year, $year, $month, $week_of_month, $solve_duplicate = false, $day = 1){
		$week = \Calendar\Week::forge_by_week_of_month($year, $month, $week_of_month, $day);
		$y = $week->iso_year();
		$w = $week->iso_week();
		
		$this->assertInternalType('int', $y);
		$this->assertInternalType('int', $w);
		$this->assertEquals($year_iso, $y);
		$this->assertEquals($week_of_year, $w);
	}
	
	public function week_of_year_and_month_provider() {
		return array(
			// year(ISO), week(ISO), year, month, week_of_month, solve_duplicate
			array(2004, 53, 2004, 12, 5, false),
			array(2004, 53, 2005, 1, 1, true),
			
			array(2005, 1, 2005, 1, 2),
			
			array(2011, 52, 2011, 12, 5, false), // これと次は異なる year, month, week_of_monthが同じISO weekに対応する
			array(2011, 52, 2012, 1, 1, true),
			
			array(2012, 1, 2012, 1, 2),
			
			array(2012, 52, 2012, 12, 5),
			
			array(2013, 1, 2012, 12, 6, false),
			array(2013, 1, 2013, 1, 1, true),
			
			array(2014, 1, 2013, 12, 6, false),
			array(2014, 1, 2014, 1, 1, true),
			
			array(2014, 4, 2014, 1, 4),
			array(2014, 5, 2014, 1, 5, false),
			array(2014, 5, 2014, 2, 1, true),
			array(2014, 8, 2014, 2, 4),
			array(2014, 9, 2014, 2, 5, false),
			array(2014, 9, 2014, 3, 1, true),
			array(2014, 13, 2014, 3, 5),
			
			// 曜日指定
			array(2013, 1, 2012, 12, 6, false, 1),
			array(2013, 1, 2013, 1, 1, false, 2),
			array(2013, 1, 2013, 1, 1, true),
			
			array(2013, 52, 2013, 12, 5, true, 1),
			array(2013, 52, 2013, 12, 5, true, 2),
			array(2014, 1, 2014, 1, 1, true, 3),
			
			array(2014, 1, 2013, 12, 6, false),
			array(2014, 1, 2013, 12, 6, false, 2),
			array(2014, 1, 2014, 1, 1, false, 3),
			array(2014, 1, 2014, 1, 1, true),
			
			array(2018, 1, 2017, 12, 6, false, 0),
			array(2018, 1, 2018, 1, 1, true, 0),
			array(2018, 1, 2018, 1, 1, false, 1),
		);
	}
	
	/**
	 * @dataProvider day_of_week_provider
	 */
	public function test_day_of_week($iso_year, $iso_week, $firstday, $offset, $date) {
		$week = \Calendar\Week::forge($iso_year, $iso_week, $firstday);
		$this->assertEquals($date, $week->day($offset)->format('Y-m-d'));
	}
	
	public function day_of_week_provider() {
		return array(
			// ISO year, ISO week, firstday, day of week, date
			array(2014, 1, 1, 0, '2013-12-30'),
			array(2014, 1, 1, 1, '2013-12-31'),
			array(2014, 1, 1, 2, '2014-01-01'),
			array(2014, 1, 1, 3, '2014-01-02'),
			array(2014, 1, 1, 4, '2014-01-03'),
			array(2014, 1, 1, 5, '2014-01-04'),
			array(2014, 1, 1, 6, '2014-01-05'),
			
			array(2014, 1, 2, 0, '2013-12-31'),
			array(2014, 1, 2, 1, '2014-01-01'),
			array(2014, 1, 2, 2, '2014-01-02'),
			array(2014, 1, 2, 3, '2014-01-03'),
			array(2014, 1, 2, 4, '2014-01-04'),
			array(2014, 1, 2, 5, '2014-01-05'),
			array(2014, 1, 2, 6, '2014-01-06'),
		);
	}
	
	/**
	 * @dataProvider days_of_week_provider
	 */
	public function test_days_of_week($iso_year, $iso_week, $firstday, $firstdate) {
		$week = \Calendar\Week::forge($iso_year, $iso_week, $firstday);
		
		$this->assertEquals(7, count($week->days()));
		
		$timestamp_firstday = strtotime($firstdate);
		$i = 0;
		foreach ($week->days() as $day) {
			$this->assertEquals(date('Y-m-d', $timestamp_firstday + $i*\Calendar\Week::DAY), $day->format('Y-m-d'));
			$i++;
		}
	}
	
	public function days_of_week_provider() {
		return array(
			// ISO year, ISO week, firstday, first date
			array(2014, 1, 1, '2013-12-30'),
			array(2014, 1, 2, '2013-12-31'),
			array(2014, 1, 3, '2014-01-01'),
			array(2014, 1, 4, '2014-01-02'),
		);
	}
	
	/**
	 * @dataProvider date_week_provider
	 */
	public function test_forge_by_date($date, $firstday, $y, $w) {
		$week = \Calendar\Week::forge_by_date($date, $firstday);
		$this->assertEquals($y, $week->iso_year());
		$this->assertEquals($w, $week->iso_week());
	}
	
	public function date_week_provider() {
		return array(
			array('2014-01-01', 0, 2014, 1),
			array('2014-01-01', 1, 2014, 1),
			array('2014-01-01', 2, 2014, 1),
			array('2014-10-12', 0, 2014, 42),
			array('2014-10-12', 1, 2014, 41),
		);
	}
	
	/**
	 * @dataProvider maxweek_provider
	 */
	public function test_max_week($year, $maxweek) {
		$this->assertEquals($maxweek, \Calendar\Week::max_week($year));
	}
	
	public function maxweek_provider(){
		return array(
			array(2004, 53),
			array(2014, 52),
			array(2015, 53),
			array(2016, 52),
			array(2017, 52),
			array(2018, 52),
			array(2019, 52),
		);
	}
}