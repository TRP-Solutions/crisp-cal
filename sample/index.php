<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);

require_once('../../git_heal-document/lib/HealDocument.php'); // https://github.com/TRP-Solutions/heal-document
$doc = new HealDocument();
$html = $doc->el('html');
$head = $html->el('head');
$head->el('title')->te('crisp-cal :: sample');

$body = $html->el('body')->el('center')->el('h3');

$body->el('a',['href'=>'today.php'])->te('Download');
$body->te(' - ');

$host = 'webcal://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$body->el('a',['href'=>$host.'weekend.php'])->te('Subscribe');

echo $doc;
