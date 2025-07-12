<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);

define('TITLE','crisp-cal :: sample');

require_once('../../heal-document/lib/HealDocument.php'); // https://github.com/TRP-Solutions/heal-document
$doc = new \TRP\HealDocument\HealDocument();
$html = $doc->el('html');
$head = $html->el('head');
$head->el('title')->te(TITLE);

$body = $html->el('body')->el('center');

$body->el('h2')->te(TITLE);

$menu = $body->el('h3');

$menu->el('a',['href'=>'today.php'])->te('Simple');
$menu->te(' - ');

$menu->el('a',['href'=>'timezone.php'])->te('Advanced');
$menu->te(' - ');

$host = 'webcal://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$menu->el('a',['href'=>$host.'weekend.php'])->te('Subscribe');

echo $doc;
