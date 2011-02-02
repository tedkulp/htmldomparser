<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class ElementTest extends PHPUnit_Framework_TestCase
{
	protected $dom;
	
	
	public function setUp()
	{
		$this->dom = new Html_Dom();
	}
	
	
	public function testInnertext()
	{
		$str = <<<HTML
<html>
    <head></head>
    <body>
        <br>
        <span>foo</span>
    </body>
</html>
HTML;

		$this->dom->load($str);
		$this->assertEquals((string) $this->dom, $str);
	}
	
	public function testChangeInnertext()
	{
		$str = <<<HTML
<html>
    <head>ok</head>
    <body>
        <br>
        <span>bar</span>
    </body>
</html>
HTML;

		$this->dom->load($str);
		$this->dom->find('span', 0)->innertext = 'bar';
		$this->assertEquals((string) $this->dom, $str);
		$this->dom->find('head', 0)->innertext = 'ok';
		$this->assertEquals((string) $this->dom, $str);
	}
}
