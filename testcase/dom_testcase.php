<?php
// $Rev$
// -----------------------------------------------------------------------------
// setup
error_reporting(E_ALL);
require_once('../html_dom_parser.php');
$html = new Html_Dom;

// -----------------------------------------------

// -----------------------------------------------

// -----------------------------------------------------------------------------
// remove no value attr test

// -----------------------------------------------

// -----------------------------------------------------------------------------
// extract text

// -----------------------------------------------------------------------------
// old fashion camel naming conventions test

// -----------------------------------------------
$str = <<<HTML
<div id="div1">
    <div id="div10"></div>
    <div id="div11"></div>
    <div id="div12"></div>
</div>
HTML;
$html->load($str);
assert($html==$str);

$e = $html->find('div#div1', 0);
assert($e->getFirstChild()->getAttribute('id')=='div10');
assert($e->getLastChild()->getAttribute('id')=='div12');
assert($e->getNextSibling()==null);
assert($e->previousSibling()==null);

// -----------------------------------------------
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
$html->load($str);
assert($html==$str);

assert($html->getElementById("div1")->hasAttribute('id')==true);
assert($html->getElementById("div1")->hasAttribute('not_exist')==false);

assert($html->getElementById("div1")->getAttribute('id')=='div1');
assert($html->getElementById("div1")->childNodes(0)->getAttribute('id')=='div10');
assert($html->getElementById("div1")->childNodes(1)->childNodes(1)->getAttribute('id')=='div111');
assert($html->getElementById("div1")->childNodes(1)->childNodes(1)->childNodes(2)->getAttribute('id')=='div1112');

assert($html->getElementsById("div1", 0)->childNodes(1)->id=='div11');
assert($html->getElementsById("div1", 0)->childNodes(1)->childNodes(1)->getAttribute('id')=='div111');
assert($html->getElementsById("div1", 0)->childNodes(1)->childNodes(1)->childNodes(1)->getAttribute('id')=='div1111');

// -----------------------------------------------
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
$html->load($str);

$ul = $html->find('ul', 0);
assert($ul->getFirstChild()->tag==='ul');

// -----------------------------------------------
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

$html->load($str);
assert($html==$str);

$ul = $html->find('ul', 0);
assert($ul->getFirstChild()->tag==='li');
assert($ul->getFirstChild()->getNextSibling()->tag==='li');
// -----------------------------------------------------------------------------
// tear down

?>