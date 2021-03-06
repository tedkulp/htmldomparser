<?php

require_once dirname(dirname(__FILE__)).'/html_dom_parser.php';

class DomTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Html_Dom
	 */
	protected $html;
	
	
	public function setUp()
	{
		$this->html = new Html_Dom();
	}
	
	public function tearDown()
	{
		$this->html->clear();
		unset($this->html);
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
	
	public function testVeryComplexNestedHtml()
	{
		$str = <<<HTML
<div id="div0">
    <div id="div00"></div>
</div>
<div id="div1">
    <div id="div10"></div>
    <div id="div11">
        <div id="div110"></div>
        <div id="div111">
            <div id="div1110"></div>
            <div id="div1111"></div>
            <div id="div1112"></div>
        </div>
        <div id="div112"></div>
    </div>
    <div id="div12"></div>
</div>
<div id="div2"></div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find("#div1", 0);
		$this->assertEquals($e->id, 'div1');
		$this->assertEquals($e->getChild(0)->id, 'div10');
		$this->assertEquals($e->getChild(1)->getChild(1)->id, 'div111');
		$this->assertEquals($e->getChild(1)->getChild(1)->getChild(2)->id, 'div1112');
	}
	
	public function testAdvancedSelectors()
	{
		$str = <<<HTML
<form name="form1" method="post" action="">
    <input type="checkbox" name="checkbox0" checked value="checkbox0">aaa<br>
    <input type="checkbox" name="checkbox1" value="checkbox1">bbb<br>
    <input type="checkbox" name="checkbox2" value="checkbox2" checked>ccc<br>
</form>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$counter = 0;
		foreach ($this->html->find('input[type=checkbox]') as $checkbox) {
			if (isset($checkbox->checked)) {
				$this->assertEquals($checkbox->value, "checkbox$counter");
				$counter += 2;
			}
		}
		
		$counter = 0;
		foreach($this->html->find('input[type=checkbox]') as $checkbox) {
			if ($checkbox->checked) {
				$this->assertEquals($checkbox->value, "checkbox$counter");
				$counter += 2;
			}
		}
		
		$es = $this->html->find('input[type=checkbox]');
		$es[1]->checked = true;
		$this->assertEquals($es[1]->outertext, '<input type="checkbox" name="checkbox1" value="checkbox1" checked>');
		$es[0]->checked = false;
		$this->assertEquals((string) $es[0], '<input type="checkbox" name="checkbox0" value="checkbox0">');
		$es[0]->checked = true;
		$this->assertEquals($es[0]->outertext, '<input type="checkbox" name="checkbox0" checked value="checkbox0">');
	}

	public function testRemoveAttributes()
	{
		$str = <<<HTML
<input type="checkbox" name="checkbox0">
<input type = "checkbox" name = 'checkbox1' value = "checkbox1">
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox0]', 0);
		$e->name = null;
		$this->assertEquals((string) $e, '<input type="checkbox">');
		$e->type = null;
		$this->assertEquals((string) $e, '<input>');
		
		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox0]', 0);
		$e->name = null;
		$this->assertEquals((string) $e, '<input type="checkbox">');
		$e->type = null;
		$this->assertEquals((string) $e, '<input>');
		
		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox1]', 0);
		$e->value = null;
		$this->assertEquals((string) $e, "<input type = \"checkbox\" name = 'checkbox1'>");
		$e->type = null;
		$this->assertEquals((string) $e, "<input name = 'checkbox1'>");
		$e->name = null;
		$this->assertEquals((string) $e, '<input>');
		
		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox1]', 0);
		$e->type = null;
		$this->assertEquals((string) $e, "<input name = 'checkbox1' value = \"checkbox1\">");
		$e->name = null;
		$this->assertEquals((string) $e, '<input value = "checkbox1">');
		$e->value = null;
		$this->assertEquals((string) $e, '<input>');
	}

	public function testRemoveNoValueAttributes()
	{
		$str = <<<HTML
<input type="checkbox" checked name='checkbox0'>
<input type="checkbox" name='checkbox1' checked>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox1]', 0);
		$e->type = NULL;
		$this->assertEquals((string) $e, "<input name='checkbox1' checked>");
		$e->name = null;
		$this->assertEquals((string) $e, "<input checked>");
		$e->checked = NULL;
		$this->assertEquals((string) $e, "<input>");
		
		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox0]', 0);
		$e->type = NULL;
		$this->assertEquals((string) $e, "<input checked name='checkbox0'>");
		$e->name = NULL;
		$this->assertEquals((string) $e, '<input checked>');
		$e->checked = NULL;
		$this->assertEquals((string) $e, '<input>');
		
		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$e = $this->html->find('[name=checkbox0]', 0);
		$e->checked = NULL;
		$this->assertEquals((string) $e, "<input type=\"checkbox\" name='checkbox0'>");
		$e->name = NULL;
		$this->assertEquals((string) $e, '<input type="checkbox">');
		$e->type = NULL;
		$this->assertEquals((string) $e, "<input>");
	}

	public function testPlaintext()
	{
		$str = <<<HTML
<b>okok</b>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$this->assertEquals($this->html->plaintext, 'okok');
	}
	
	public function testNestedHtmlPlaintext()
	{
		$str = <<<HTML
<div><b>okok</b></div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$this->assertEquals($this->html->plaintext, 'okok');
	}
	
	public function testUnclosedHtmlPlaintext()
	{
		$str = <<<HTML
<div><b>okok</b>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);

		$this->assertEquals($this->html->plaintext, 'okok');
	}
	
	public function testInvalidHtmlPlaintext()
	{
		$str = <<<HTML
<b>okok</b></div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$this->assertEquals($this->html->plaintext, 'okok</div>');
	}
	
	public function testAttributeManipulation()
	{
		$str = <<<HTML
<input type="checkbox" id="checkbox" name="checkbox" value="checkbox" checked>
<input type="checkbox" id="checkbox1" name="checkbox1" value="checkbox1">
<input type="checkbox" id="checkbox2" name="checkbox2" value="checkbox2" checked>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$this->assertTrue($this->html->getElementByTagName('input')->hasAttribute('checked'));
		$this->assertFalse($this->html->getElementsByTagName('input', 1)->hasAttribute('checked'));
		$this->assertFalse($this->html->getElementsByTagName('input', 1)->hasAttribute('not_exist'));
		
		$this->assertEquals($this->html->find('input', 0)->value, $this->html->getElementByTagName('input')->getAttribute('value'));
		$this->assertEquals($this->html->find('input', 1)->value, $this->html->getElementsByTagName('input', 1)->getAttribute('value'));
		
		$this->assertEquals($this->html->find('#checkbox1', 0)->value, $this->html->getElementById('checkbox1')->getAttribute('value'));
		$this->assertEquals($this->html->find('#checkbox2', 0)->value, $this->html->getElementsById('checkbox2', 0)->getAttribute('value'));
		
		$e = $this->html->find('[name=checkbox]', 0);
		$this->assertEquals($e->getAttribute('value'), 'checkbox');
		$this->assertTrue($e->getAttribute('checked'));
		$this->assertEmpty($e->getAttribute('not_exist'));
		
		$e->setAttribute('value', 'okok');
		$this->assertEquals((string) $e, '<input type="checkbox" id="checkbox" name="checkbox" value="okok" checked>');
		
		$e->setAttribute('checked', false);
		$this->assertEquals((string) $e, '<input type="checkbox" id="checkbox" name="checkbox" value="okok">');
		
		$e->setAttribute('checked', true);
		$this->assertEquals((string) $e, '<input type="checkbox" id="checkbox" name="checkbox" value="okok" checked>');
		
		$e->removeAttribute('value');
		$this->assertEquals((string) $e, '<input type="checkbox" id="checkbox" name="checkbox" checked>');
		
		$e->removeAttribute('checked');
		$this->assertEquals((string) $e, '<input type="checkbox" id="checkbox" name="checkbox">');
	}

	public function testDivFamily()
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
		$this->assertEquals($e->getFirstChild()->getAttribute('id'), 'div10');
		$this->assertEquals($e->getLastChild()->getAttribute('id'), 'div12');
		$this->assertNull($e->getNextSibling());
		$this->assertNull($e->getPrevSibling());
	}
	
	public function testDivGreaterFamily()
	{
		$str = <<<HTML
<div id="div0">
    <div id="div00"></div>
</div>
<div id="div1">
    <div id="div10"></div>
    <div id="div11">
        <div id="div110"></div>
        <div id="div111">
            <div id="div1110"></div>
            <div id="div1111"></div>
            <div id="div1112"></div>
        </div>
        <div id="div112"></div>
    </div>
    <div id="div12"></div>
</div>
<div id="div2"></div>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$this->assertTrue($this->html->getElementById("div1")->hasAttribute('id'));
		$this->assertFalse($this->html->getElementById("div1")->hasAttribute('not_exist'));
		
		$this->assertEquals($this->html->getElementById("div1")->getAttribute('id'), 'div1');
		$this->assertEquals($this->html->getElementById("div1")->getChild(0)->getAttribute('id'), 'div10');
		$this->assertEquals($this->html->getElementById("div1")->getChild(1)->getChild(1)->getAttribute('id'), 'div111');
		$this->assertEquals($this->html->getElementById("div1")->getChild(1)->getChild(1)->getChild(2)->getAttribute('id'), 'div1112');
		
		$this->assertEquals($this->html->getElementsById("div1", 0)->getChild(1)->id, 'div11');
		$this->assertEquals($this->html->getElementsById("div1", 0)->getChild(1)->getChild(1)->getAttribute('id'), 'div111');
		$this->assertEquals($this->html->getElementsById("div1", 0)->getChild(1)->getChild(1)->getChild(1)->getAttribute('id'), 'div1111');
	}

	public function testTagType()
	{
		$str = <<<HTML
<ul class="menublock">
    </li>
        <ul>
            <li>
                <a href="http://www.cyberciti.biz/tips/pollsarchive">Polls Archive</a>
            </li>
        </ul>
    </li>
</ul>
HTML;

		$this->html->load($str);
		
		$ul = $this->html->find('ul', 0);
		$this->assertEquals($ul->getFirstChild()->tag, 'ul');
	}
	
	public function testNestedTagTypes()
	{
		$str = <<<HTML
<ul>
    <li>Item 1 
        <ul>
            <li>Sub Item 1 </li>
            <li>Sub Item 2 </li>
        </ul>
    </li>
    <li>Item 2 </li>
</ul>
HTML;

		$this->html->load($str);
		$this->assertEquals((string) $this->html, $str);
		
		$ul = $this->html->find('ul', 0);
		$this->assertEquals($ul->getFirstChild()->tag, 'li');
		$this->assertEquals($ul->getFirstChild()->getNextSibling()->tag, 'li');
	}
}
