<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);
require_once __DIR__.'/../lib/CrispCal.php';

date_default_timezone_set('Europe/Copenhagen');

$cal = new CrispCal();
$event = $cal->event('travel@crisp-cal');

$start = new Datetime('10:15',new DateTimeZone('Europe/Lisbon'));
$event->start($start);

$end = new Datetime('13:45',new DateTimeZone('Europe/Tallinn'));
$event->end($end);

$event->summary('CrispCal: Travel');

CrispCalOutput::header('travel');
echo $cal;
