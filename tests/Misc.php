<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class MiscTest extends PHPUnit_Framework_TestCase
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
	
	
	public function testLastElement()
	{
		$str = <<<HTML
<img class="class0" id="id0" src="src0">
<img class="class1" id="id1" src="src1">
<img class="class2" id="id2" src="src2">
HTML;

		$this->dom->load($str);
		
		$e = $this->dom->find('img');
		$this->assertEquals(count($e), 3);
		$this->assertEquals($e[0]->src, 'src0');
		$this->assertEquals($e[1]->src, 'src1');
		$this->assertEquals($e[2]->src, 'src2');
		$this->assertEmpty($e[0]->innertext);
		$this->assertEmpty($e[1]->innertext);
		$this->assertEmpty($e[2]->innertext);
		$this->assertEquals($e[0]->outertext, '<img class="class0" id="id0" src="src0">');
		$this->assertEquals($e[1]->outertext, '<img class="class1" id="id1" src="src1">');
		$this->assertEquals($e[2]->outertext, '<img class="class2" id="id2" src="src2">');
		$this->assertEquals($this->dom->find('img', 0)->src, 'src0');
		$this->assertEquals($this->dom->find('img', 1)->src, 'src1');
		$this->assertEquals($this->dom->find('img', 2)->src, 'src2');
		$this->assertNull($this->dom->find('img', 3));
		$this->assertNull($this->dom->find('img', 99));
		$this->assertEquals($this->dom->save(), $str);
	}
	
	public function testErrorTag()
	{
		$str = <<<HTML
<img class="class0" id="id0" src="src0"><p>p1</p>
<img class="class1" id="id1" src="src1"><p>
<img class="class2" id="id2" src="src2"></a></div>
HTML;

		$dom = str_get_html($str);
		$es = $dom->find('img');
		$this->assertEquals(count($es), 3);
		$this->assertEquals($es[0]->src, 'src0');
		$this->assertEquals($es[1]->src, 'src1');
		$this->assertEquals($es[2]->src, 'src2');
		
		$es = $dom->find('p');
		$this->assertEquals($es[0]->innertext, 'p1');
		$this->assertEquals((string) $dom, $str);
	}
}
