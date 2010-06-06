<?php
include_once('../html_dom_parser.php');

echo file_get_html('http://www.google.com/')->plaintext;
?>