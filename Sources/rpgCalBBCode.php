<?php

/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.2
* @license BSD 3-clause
*/

require_once('../SSI.php');
require_once($sourcedir.'/rpgCal.php');
require_once($sourcedir.'/Subs-rpgCal.php');
global $smcFunc, $db_prefix, $modSettings;

$year=intval($_POST['rpgCalYear']);
$month=intval($_POST['rpgCalMonth']);
$rpgCalEventMultiple=array(0,255);
list($rpgCalEvents,$rpgCalLegend, $rpgCalEventmultiple)=rpgCal_event($year,$month, $rpgCalEventMultiple);
echo rpgCalDrawBBCode($month,$year,$aoevents);
ksort($events);
$rpgCalLegend='<ul>';
	foreach ($events as $key => $value) {
		$rpgCalLegend.= '<li style="color:'.$value['color'].'">[color='.$value['color'].'][b]'.date("F", mktime(0, 0, 0, $month, 01, $year)).' '.$key.'-'.$value['title'].'[/b][/color]</li>'.PHP_EOL;
	}
	$rpgCalLegend.='</ul>';
echo $rpgCalLegend;

/* draws a calendar */
function rpgCalDrawBBCode($month,$year,$events){
	global $txt;
	/* draw table */
	$calendar = '<pre>[td][left][quote][center][b]'.date("F", mktime(0, 0, 0, $month, 01, $year)).'[/b][/center][pre]'. PHP_EOL;

	/* table headings */
	$headings = $txt['rpg_cal_bbcode_headings'];
	$calendar.= implode(' ',$headings) . PHP_EOL;

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '   ';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		if ($list_day<=9):
			$calendar.=' ';
		endif;
		if (isset($events[$list_day])):
			$calendar.= '[color='.$events[$list_day]['color'].']'.$list_day.'[/color] ';
			else:
				$calendar.= $list_day.' ';
		endif;
		if($running_day == 6):
			$calendar.= PHP_EOL;
			if(($day_counter+1) != $days_in_month):
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '   ';
		endfor;
	endif;

	/* final row */
	$calendar.= PHP_EOL .'[/pre][/quote][/left][/td]</pre>'.PHP_EOL;
	
	/* all done, return result */
	return $calendar;
}
?>