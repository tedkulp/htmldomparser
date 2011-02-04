<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class ElementTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Html_Dom
	 */
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
	
	public function testSimpleManipulation()
	{
		$str = <<<HTML
<b>foo</b>
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('b text', 0);
		$this->assertEquals($e->innertext, 'foo');
		$this->assertEquals($e->outertext, 'foo');
		$e->innertext = 'bar';
		$this->assertEquals($e->innertext, 'bar');
		$this->assertEquals($e->outertext, 'bar');
		
		$e = $this->dom->find('b', 0);
		$this->assertEquals($e->innertext, 'bar');
		$this->assertEquals($e->outertext, '<b>bar</b>');
	}
	
	public function testOutertext()
	{
		$str = <<<HTML
<table>
<tr><th>Head1</th><th>Head2</th><th>Head3</th></tr>
<tr><td>1</td><td>2</td><td>3</td></tr>
</table>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('tr', 0)->outertext, '<tr><th>Head1</th><th>Head2</th><th>Head3</th></tr>');
		$this->assertEquals($this->dom->find('tr', 1)->outertext, '<tr><td>1</td><td>2</td><td>3</td></tr>');
	}
	
	public function testInvalidHtmlOutertext()
	{
		$str = <<<HTML
<table><tr><th>Head1</th><th>Head2</th><th>Head3</th><tr><td>1</td><td>2</td><td>3</td></table>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('tr', 0)->outertext, '<tr><th>Head1</th><th>Head2</th><th>Head3</th>');
		$this->assertEquals($this->dom->find('tr', 1)->outertext, '<tr><td>1</td><td>2</td><td>3</td>');
	}

	public function testListOutertext()
	{
		$str = <<<HTML
<ul><li><b>li11</b></li><li><b>li12</b></li></ul><ul><li><b>li21</b></li><li><b>li22</b></li></ul>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('ul', 0)->outertext, '<ul><li><b>li11</b></li><li><b>li12</b></li></ul>');
		$this->assertEquals($this->dom->find('ul', 1)->outertext, '<ul><li><b>li21</b></li><li><b>li22</b></li></ul>');
	}

	public function testListInvalidHtmlOutertext()
	{
		$str = <<<HTML
<ul><li><b>li11</b></li><li><b>li12</b></li><ul><li><b>li21</b></li><li><b>li22</b></li>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('ul', 0)->outertext, '<ul><li><b>li11</b></li><li><b>li12</b></li><ul><li><b>li21</b></li><li><b>li22</b></li>');
		$this->assertEquals($this->dom->find('ul', 1)->outertext, '<ul><li><b>li21</b></li><li><b>li22</b></li>');

		$str = <<<HTML
<ul><li><b>li11</b><li><b>li12</b></li><ul><li><b>li21</b></li><li><b>li22</b>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('ul', 0)->outertext, '<ul><li><b>li11</b><li><b>li12</b></li><ul><li><b>li21</b></li><li><b>li22</b>');
		$this->assertEquals($this->dom->find('ul', 1)->outertext, '<ul><li><b>li21</b></li><li><b>li22</b>');
	}

	public function testTableOutertext()
	{
		$str = <<<HTML
<table>
<tr><th>Head1</th><th>Head2</th><th>Head3</th></tr>
<tr><td>1</td><td>2</td><td>3</td></tr>
</table>
HTML;

		$this->dom->load($str);
		$this->assertEquals($this->dom->find('tr', 0)->outertext, '<tr><th>Head1</th><th>Head2</th><th>Head3</th></tr>');
		$this->assertEquals($this->dom->find('tr', 1)->outertext, '<tr><td>1</td><td>2</td><td>3</td></tr>');
	}

	public function testReplacement()
	{
		$str = <<<HTML
<div class="class1" id="id2" ><div class="class2">ok</div></div>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('div');
		$this->assertEquals(count($e), 2);
		$this->assertEquals($e[0]->innertext, '<div class="class2">ok</div>');
		$this->assertEquals($e[0]->outertext, '<div class="class1" id="id2" ><div class="class2">ok</div></div>');
		
		$e[0]->class = 'class_test';
		$this->assertTrue(isset($e[0]->class));
		$this->assertFalse(isset($e[0]->okok));
		
		$e[0]->class = 'class_test';
		$this->assertEquals($e[0]->outertext, '<div class="class_test" id="id2" ><div class="class2">ok</div></div>');
		
		$e[0]->tag = 'span';
		$this->assertEquals($e[0]->outertext, '<span class="class_test" id="id2" ><div class="class2">ok</div></span>');
	}

	public function testRemoveAttribute()
	{
		$str = <<<HTML
<div class="class1" id="id2" ><div class="class2">ok</div></div>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('div');
		unset($e[0]->attr['class']);
		$this->assertEquals($e[0]->outertext, '<div id="id2" ><div class="class2">ok</div></div>');
	}
	
	public function testInnertextManipulation()
	{
		$str = <<<HTML
<select name=something><options>blah</options><options>blah2</options></select>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('select[name=something]', 0);
		$e->innertext = '';
		$this->assertEquals($e->outertext, '<select name=something></select>');
	}
	
	public function testNestedReplacement()
	{
		$str = <<<HTML
<div class="class0" id="id0" ><div class="class1">ok</div></div>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('div');
		$this->assertEquals(count($e), 2);
		$this->assertEquals($e[0]->innertext, '<div class="class1">ok</div>');
		$this->assertEquals($e[0]->outertext, '<div class="class0" id="id0" ><div class="class1">ok</div></div>');
		$this->assertEquals($e[1]->innertext, 'ok');
		$this->assertEquals($e[1]->outertext, '<div class="class1">ok</div>');
		
		$e[1]->innertext = 'okok';
		$this->assertEquals($e[1]->outertext, '<div class="class1">okok</div>');
		$this->assertEquals($e[0]->outertext, '<div class="class0" id="id0" ><div class="class1">okok</div></div>');
		$this->assertEquals((string) $this->dom, '<div class="class0" id="id0" ><div class="class1">okok</div></div>');
		
		$e[1]->class = 'class_test';
		$this->assertEquals($e[1]->outertext, '<div class="class_test">okok</div>');
		$this->assertEquals($e[0]->outertext, '<div class="class0" id="id0" ><div class="class_test">okok</div></div>');
		$this->assertEquals((string) $this->dom, '<div class="class0" id="id0" ><div class="class_test">okok</div></div>');
		
		$e[0]->class = 'class_test';
		$this->assertEquals($e[0]->outertext, '<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');
		$this->assertEquals((string) $this->dom, '<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');
		
		$e[0]->innertext = 'okokok';
		$this->assertEquals($e[0]->outertext, '<div class="class_test" id="id0" >okokok</div>');
		$this->assertEquals((string) $this->dom, '<div class="class_test" id="id0" >okokok</div>');
	}

	public function testParagraphs()
	{
		$str = <<<HTML
<div class="class0">
    <p>ok0<a href="#">link0</a></p>
    <div class="class1"><p>ok1<a href="#">link1</a></p></div>
    <div class="class2"></div>
    <p>ok2<a href="#">link2</a></p>
</div>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('p');
		$this->assertEquals($e[0]->innertext, 'ok0<a href="#">link0</a>');
		$this->assertEquals($e[1]->innertext, 'ok1<a href="#">link1</a>');
		$this->assertEquals($e[2]->innertext, 'ok2<a href="#">link2</a>');
		$this->assertEquals($this->dom->find('p', 0)->plaintext, 'ok0link0');
		$this->assertEquals($this->dom->find('p', 1)->plaintext, 'ok1link1');
		$this->assertEquals($this->dom->find('p', 2)->plaintext, 'ok2link2');
		
		$count = 0;
		foreach ($this->dom->find('p') as $p) {
		    $a = $p->find('a');
		    $this->assertEquals($a[0]->innertext, 'link'.$count);
		    ++$count;
		}
		
		$e = $this->dom->find('p a');
		$this->assertEquals($e[0]->innertext, 'link0');
		$this->assertEquals($e[1]->innertext, 'link1');
		$this->assertEquals($e[2]->innertext, 'link2');
		$this->assertEquals($this->dom->find('p a', 0)->plaintext, 'link0');
		$this->assertEquals($this->dom->find('p a', 1)->plaintext, 'link1');
		$this->assertEquals($this->dom->find('p a', 2)->plaintext, 'link2');
		
		$this->assertEquals((string) $this->dom, $str);
	}

	public function testEmbed()
	{
		$str = <<<HTML
<EMBED 
   SRC="../graphics/sounds/1812over.mid"
   HEIGHT=60 WIDTH=144>
HTML;

		$this->dom->load($str);
		$e = $this->dom->find('embed', 0);
		$this->assertEquals($e->src, '../graphics/sounds/1812over.mid');
		$this->assertEquals($e->height, '60');
		$this->assertEquals($e->width, '144');
		$this->assertEquals((string) $this->dom, strtolower($str));
	}
}
