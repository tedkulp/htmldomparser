<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class InvalidTest extends PHPUnit_Framework_TestCase
{
	protected $dom;
	
	
	public function setUp()
	{
		$this->dom = new Html_Dom();
	}
	
	public function tearDown()
	{
		$this->dom->clear();
		unset($this->dom);
	}
	
	
	public function testSelfClosingTags()
	{
		$str = <<<HTML
<hr>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$this->assertEquals($e->outertext, '<hr id="foo">');
	}
	
	public function testSelfClosingTags2()
	{
		$str = <<<HTML
<hr/>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$this->assertEquals($e->outertext, '<hr id="foo"/>');
	}
	
	public function testSelfClosingTags3()
	{
		$str = <<<HTML
<hr />
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$this->assertEquals($e->outertext, '<hr id="foo" />');
	}
	
	public function testSelfClosingTags4()
	{
		$str = <<<HTML
<hr>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" class="bar">');
	}
	
	public function testSelfClosingTags5()
	{
		$str = <<<HTML
<hr/>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" class="bar"/>');
	}

	public function testSelfClosingTags6()
	{
		$str = <<<HTML
<hr />
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->id = 'foo';
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" class="bar" />');
	}
	
	public function testSelfClosingTags7()
	{
		$str = <<<HTML
<hr id="foo" kk=ll>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" kk=ll class="bar">');
	}
	
	public function testSelfClosingTags8()
	{
		$str = <<<HTML
<hr id="foo" kk="ll"/>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" kk="ll" class="bar"/>');
	}
	
	public function testSelfClosingTags9()
	{
		$str = <<<HTML
<hr id="foo" kk=ll />
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('hr', 0);
		$e->class = 'bar';
		$this->assertEquals($e->outertext, '<hr id="foo" kk=ll class="bar" />');
	}
	
	public function testSelfClosingTags10()
	{
		$str = <<<HTML
<div><nobr></div>
HTML;
		$this->dom->load($str);
		
		$e = $this->dom->find('nobr', 0);
		$this->assertEquals($e->outertext, '<nobr>');
	}
	
	public function testOptionalClosingTags()
	{
		$str = <<<HTML
<body>
</b><.b></a>
</body>
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals($this->dom->find('body', 0)->outertext, $str);
	}

	public function testOptionalClosingTags2()
	{
		$str = <<<HTML
<html>
    <body>
        <a>foo</a>
        <a>foo2</a>
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals((string) $this->dom, $str);
		$this->assertEquals($this->dom->find('html body a', 1)->innertext, 'foo2');
	}
	
	public function testEmptyString()
	{
		$str = <<<HTML
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals((string) $this->dom, $str);
		$this->assertNull($this->dom->find('html a', 1));
	}
	
	public function testOutmostTag()
	{
		$str = <<<HTML
<body>
<div>
</body>
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals((string) $this->dom, $str);
		$this->assertEquals($this->dom->find('body', 0)->outertext, $str);
	}
	
	public function testUnopenedClosingTag()
	{
		$str = <<<HTML
<body>
<div> </a> </div>
</body>
HTML;
		$this->dom = str_get_html($str);
		
		$this->assertEquals($this->dom->find('body', 0)->outertext, $str);
	}
	
	public function testUnclosedTableTags()
	{
		$str = <<<HTML
<table>
    <tr>
        <td><b>aa</b>
    <tr>
        <td><b>bb</b>
</table>
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals((string) $this->dom, $str);
	}
	
	public function testUnclosedTableTags2()
	{
		$str = <<<HTML
<table>
<tr><td>1<td>2<td>3
</table>
HTML;

		$this->dom = str_get_html($str);
		
		$this->assertEquals(count($this->dom->find('td')), 3);
		$this->assertEquals($this->dom->find('td', 0)->innertext, '1');
		$this->assertEquals($this->dom->find('td', 0)->outertext, '<td>1');
		$this->assertEquals($this->dom->find('td', 1)->innertext, '2');
		$this->assertEquals($this->dom->find('td', 1)->outertext, '<td>2');
		$this->assertEquals($this->dom->find('td', 2)->innertext, "3\r\n");
		$this->assertEquals($this->dom->find('td', 2)->outertext, "<td>3\r\n");
	}
}
