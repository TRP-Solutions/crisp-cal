<?php
/*
CrispCal is licensed under the Apache License 2.0 license
https://github.com/TRP-Solutions/crisp-cal/blob/master/LICENSE
*/
declare(strict_types=1);

class CrispCal {
	private array $event = [];
	private string $name;

	public function event(?string $uid = null) : CrispCalEvent {
		$event = new CrispCalEvent($uid);
		$this->event[] = $event;
		return $event;
	}

	public function name(string $value) : void {
		$this->name = $value;
	}

	public function __toString() : string {
		$str = CrispCalOutput::text('BEGIN','VCALENDAR');
		$str .= CrispCalOutput::text('VERSION','2.0');
		$str .= CrispCalOutput::text('PRODID','CrispCal[https://github.com/TRP-Solutions/crisp-cal]');

		if(isset($this->name)) $str .= CrispCalOutput::text('X-WR-CALNAME',$this->name);

		foreach($this->event as $event) {
			$str .= (string) $event;
		}

		$str .= CrispCalOutput::text('END','VCALENDAR');
		return $str;
	}
}

class CrispCalEvent {
	// Relationship Component
	private string $uid;
	private array $alarm = [];

	// Date and Time Component
	private object $dtstamp;
	private object $dtstart;
	private object $dtend;
	private bool $fullday = false;

	// Descriptive Component
	private string $description;
	private string $summary;
	private string $location;
	private string $url;

	public function __construct(?string $uid = null) {
		$this->uid = ($uid) ? $uid : md5((string) rand());
		$this->dtstamp = new DateTime();
		$this->dtstamp->setTimezone(new DateTimeZone('UTC'));
	}
	public function start(DateTime|string $date) : void {
		if($date instanceof DateTime) {
			$this->dtstart = clone $date;
		} else {
			$this->dtstart = new DateTime($date);
		}
		$this->dtstart->setTimezone(new DateTimeZone('UTC'));
	}
	public function end(DateTime|string $date) : void {
		if(!isset($this->dtstart)) {
			throw new \Exception('CrispCalEvent start not set');
		}
		if($date instanceof DateTime) {
			$this->dtend = clone $date;
		} else {
			$this->dtend = new DateTime($date);
		}
		$this->dtend->setTimezone(new DateTimeZone('UTC'));
	}
	public function duration(DateInterval|string $input) : void {
		if(!isset($this->dtstart)) {
			throw new \Exception('CrispCalEvent start not set');
		}
		if($input instanceof DateInterval) {
			$interval = $input;
		}
		else {
			$interval = DateInterval::createFromDateString($input);
		}
		$this->dtend = clone $this->dtstart;
		$this->dtend->add($interval);
		$this->dtend->setTimezone(new DateTimeZone('UTC'));
	}
	public function summary(string $value) : void {
		$this->summary = $value;
	}
	public function location(string $value) : void {
		$this->location = $value;
	}
	public function description(string $value) : void {
		$this->description = $value;
	}
	public function url(string $value) : void {
		$this->url = $value;
	}
	public function fullday(bool $fullday = true) : void {
		$this->fullday = $fullday;
	}
	public function alarm(DateInterval|string $trigger) : CrispCalAlarm {
		$alarm = new CrispCalAlarm($trigger);
		$this->alarm[] = $alarm;
		return $alarm;
	}

	public function __toString() : string {
		$str = CrispCalOutput::text('BEGIN','VEVENT');
		$str .= CrispCalOutput::text('UID',$this->uid);
		$str .= CrispCalOutput::datetime('DTSTAMP',$this->dtstamp);
		if($this->fullday) {
			$str .= CrispCalOutput::date('DTSTART',$this->dtstart);
			$str .= CrispCalOutput::date('DTEND',$this->dtend);
		}
		else {
			$str .= CrispCalOutput::datetime('DTSTART',$this->dtstart);
			$str .= CrispCalOutput::datetime('DTEND',$this->dtend);
		}

		if(isset($this->summary)) $str .= CrispCalOutput::text('SUMMARY',$this->summary);
		if(isset($this->location)) $str .= CrispCalOutput::text('LOCATION',$this->location);
		if(isset($this->description)) $str .= CrispCalOutput::text('DESCRIPTION',$this->description);
		if(isset($this->url)) $str .= CrispCalOutput::text('URL',$this->url);

		foreach($this->alarm as $alarm) {
			$str .= (string) $alarm;
		}

		$str .= CrispCalOutput::text('END','VEVENT');
		return $str;
	}
}

class CrispCalAlarm {
	private string $description;
	private DateInterval $trigger;

	public function __construct(DateInterval|string $trigger) {
		if($trigger instanceof DateInterval) {
			$this->trigger = $trigger;
		}
		else {
			$this->trigger = DateInterval::createFromDateString($trigger);
		}
	}

	public function description(string $value) : void {
		$this->description = $value;
	}

	public function __toString() : string {
		$str = CrispCalOutput::text('BEGIN','VALARM');
		$str .= CrispCalOutput::duration('TRIGGER',$this->trigger);
		$str .= CrispCalOutput::text('ACTION','DISPLAY');

		if(isset($this->description)) $str .= CrispCalOutput::text('DESCRIPTION',$this->description);

		$str .= CrispCalOutput::text('END','VALARM');
		return $str;
	}
}

class CrispCalOutput {
	const NEWLINE = "\r\n";

	public static function text(string $parameter, string $value) : string {
		$replace_pairs = ['\\'=>'\\\\',';'=>'\\;',','=>'\\,',"\r"=>'',"\n"=>'\\n'];
		return $parameter.':'.strtr($value,$replace_pairs).self::NEWLINE;
	}
	public static function datetime(string $parameter, object $value) : string {
		return $parameter.':'.$value->format('Ymd\THisp').self::NEWLINE;
	}
	public static function date(string $parameter, object $value) : string {
		return $parameter.':'.$value->format('Ymd').self::NEWLINE;
	}
	public static function duration(string $parameter, object $value) : string {
		$value->invert = 1;
		$str = $value->format('%rP%yY%mM%dDT%hH%iM%sS');
		$str = str_replace(['M0S','H0M','T0H','M0D','Y0M','P0Y'],['M','H','T','M','Y','P'],$str);
		return $parameter.':'.$str.self::NEWLINE;
	}

	public static function header(?string $filename = null) : void {
		header('Content-Type: text/calendar; charset=utf-8');
		if($filename) {
			header("Content-Disposition: inline; filename*=UTF-8''".rawurlencode($filename.'.ics'));
		}
	}
}
