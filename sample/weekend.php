<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);
require_once __DIR__.'/../lib/CrispCal.php';

$cal = new CrispCal();
$cal->name('CrispCal Work');

$event = $cal->event('development@crisp-cal');
$event->summary('CrispCal: Development');
$event->location('Kelingking Beach');
$event->url('https://github.com/TRP-Solutions/crisp-cal/issues/');
$event->description('Glad to have you helping 🤩'.PHP_EOL.'Updated: '.date('r'));

$time = new Datetime('this saturday');
$event->start($time);

$time->modify('+2 day');
$event->end($time);

$event->fullday();
echo $cal;
