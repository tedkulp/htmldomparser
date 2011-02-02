<?php

require_once 'Dom.php';
require_once 'Element.php';
require_once 'Misc.php';
 
class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('HtmlDomParser Tests');

		$suite->addTestSuite('DomTest');
		$suite->addTestSuite('ElementTest');
		$suite->addTestSuite('MiscTest');

		return $suite;
	}
}
