<?php

Autoloader::add_core_namespace('Calendar');

Autoloader::add_classes(array(
	'Calendar\\Day'  => __DIR__.'/classes/day.php',
	'Calendar\\Week'  => __DIR__.'/classes/week.php',
	'Calendar\\Month'  => __DIR__.'/classes/month.php',
	'Calendar\\Year'  => __DIR__.'/classes/year.php',
));
