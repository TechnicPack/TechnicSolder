<?php

class TimeUtils {

	public static function getDiff($from, $to) {
		$dStart = new DateTime(date('Y-m-d H:i',$from));
		$dEnd = new DateTime(date('Y-m-d H:i',$to));
		$dDiff = $dStart->diff($dEnd);

		if ($dDiff->days > 0) {
			$time = $dDiff->days;
			$word = "days";
		} else if ($dDiff->h > 0) {
			$time = $dDiff->h;
			$word = "hours";
		} else if ($dDiff->i > 0) {
			$time = $dDiff->i;
			$word = "minutes";
		} else {
			return "Less than a minute ago";
		}

		if ($time == 1)
		{
			$word = Str::singular($word);
		}
		return $time . " " . $word . " ago";
	}

	public static function getFancyDate($db_date) {
		return date("m-d-Y",strtotime($db_date));
	}

	public static function getSlashDate($db_date) {
		return date("m/d/y", strtotime($db_date));
	}

	public static function getTimeStampDate($db_date) {
		return strtotime($db_date);
	}
}

?>