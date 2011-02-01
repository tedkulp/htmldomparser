<?php

require_once 'Dom.php';
 
class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('HtmlDomParser Tests');

		$suite->addTestSuite('DomTest');

		return $suite;
	}
}
