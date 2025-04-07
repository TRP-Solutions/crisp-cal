<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);
require_once __DIR__.'/../lib/CrispCal.php';

date_default_timezone_set('Europe/Copenhagen');

$cal = new CrispCal();
$event = $cal->event('meeting@crisp-cal');
$event->start('10:00');
$event->duration('2 hours');
$event->summary('CrispCal: Strategical meeting');

$event->alarm('12 minutes');
$event->alarm(new DateInterval('PT7M'));

CrispCalOutput::header('meeting');
echo $cal;
