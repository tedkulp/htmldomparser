<?php
// $Rev$
// -----------------------------------------------------------------------------
// setup
error_reporting(E_ALL);
require_once('../html_dom_parser.php');
$dom = new Html_Dom;

// -----------------------------------------------------------------------------
// test problem of last emelemt not found
$str = <<<HTML
<img src="src0"><p>foo</p><img src="src2">
HTML;

function callback_1($e) {
    if ($e->tag==='img')
        $e->outertext = '';
}

$dom->load($str);
$dom->setCallback('callback_1');
assert($dom=='<p>foo</p>');

// -----------------------------------------------
// innertext test
function callback_2($e) {
    if ($e->tag==='p')
        $e->innertext = 'bar';
}

$dom->load($str);
$dom->setCallback('callback_2');
assert($dom=='<img src="src0"><p>bar</p><img src="src2">');

// -----------------------------------------------
// attributes test
function callback_3($e) {
    if ($e->tag==='img')
        $e->src = 'foo';
}

$dom->load($str);
$dom->setCallback('callback_3');
assert($dom=='<img src="foo"><p>foo</p><img src="foo">');

function callback_4($e) {
    if ($e->tag==='img')
        $e->id = 'foo';
}

$dom->setCallback('callback_4');
assert($dom=='<img src="foo" id="foo"><p>foo</p><img src="foo" id="foo">');

// -----------------------------------------------
// attributes test2
//$dom = str_get_dom($str);
$dom->load($str);
$dom->removeCallback();
$dom->find('img', 0)->id = "foo";
assert($dom=='<img src="src0" id="foo"><p>foo</p><img src="src2">');

function callback_5($e) {
    if ($e->src==='src0')
        unset($e->id);
}

$dom->setCallback('callback_5');
assert($dom==$str);

// -----------------------------------------------------------------------------
// tear down
$dom->clear();
unset($dom);
?>