<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class DomTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->html = new Html_Dom();
	}
	
	public function testEmptyDocument()
	{
		$this->html->load('');
		
		$e = $this->html->root;
		$this->assertNull($e->getFirstChild());
		$this->assertNull($e->getLastChild());
		$this->assertNull($e->getNextSibling());
		$this->assertNull($e->getPrevSibling());
	}
	
	public function testSimpleString()
	{
		$str = '<div id="div1"></div>';
		$this->html->load($str);
		
		$e = $this->html->root;
		$this->assertEquals($e->getFirstChild()->id, 'div1');
		$this->assertEquals($e->getLastChild()->id, 'div1');
		$this->assertNull($e->getNextSibling());
		$this->assertNull($e->getPrevSibling());
		$this->assertEquals($e->plaintext, '');
		$this->assertEquals($e->innertext, $str);
		$this->assertEquals($e->outertext, $str);
	}
	
	public function testComplexHtml()
	{
		$str = <<<HTML
<div id="div1">
    <div id="div10"></div>
    <div id="div11"></div>
    <div id="div12"></div>
</div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('div#div1', 0);
		$this->assertTrue(isset($e->id));
		$this->assertFalse(isset($e->_not_exist));
		$this->assertEquals($e->getFirstChild()->id, 'div10');
		$this->assertEquals($e->getLastChild()->id, 'div12');
		$this->assertNull($e->getNextSibling());
		$this->assertNull($e->getPrevSibling());
	}
	
	public function testComplexNestedHtml()
	{
		$str = <<<HTML
<div id="div0">
    <div id="div00"></div>
</div>
<div id="div1">
    <div id="div10"></div>
    <div id="div11"></div>
    <div id="div12"></div>
</div>
<div id="div2"></div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('div#div1', 0);
		$this->assertEquals($e->getFirstChild()->id, 'div10');
		$this->assertEquals($e->getLastChild()->id, 'div12');
		$this->assertEquals($e->getNextSibling()->id, 'div2');
		$this->assertEquals($e->getPrevSibling()->id, 'div0');
		
		$e = $this->html->find('div#div2', 0);
		$this->assertNull($e->getFirstChild());
		$this->assertNull($e->getLastChild());
		
		$e = $this->html->find('div#div0 div#div00', 0);
		$this->assertNull($e->getFirstChild());
		$this->assertNull($e->getLastChild());
		$this->assertNull($e->getNextSibling());
		$this->assertNull($e->getPrevSibling());
	}
}
