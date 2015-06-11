<?php
/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.2
* @license BSD 3-clause
*/
// First of all, we make sure we are accessing the source file via SMF so that people can not directly access the file. 
if (!defined('SMF'))
  die('Hack Attempt...');
  
function rpgCalMain()
{

	// Second, give ourselves access to all the global variables we will need for this action
	global $context, $scripturl, $txt, $smcFunc;

	// Third, Load the specialty template for this action.
	loadTemplate('rpgCal');
	loadLanguage('rpgCal');

	//Fourth, Come up with a page title for the main page
	$context['page_title'] = $txt['rpg_cal_page_title'];
	$context['page_title_html_safe'] = $smcFunc['htmlspecialchars'](un_htmlspecialchars($context['page_title']));


	//Fifth, define the navigational link tree to be shown at the top of the page.
	$context['linktree'][] = array(
  		'url' => $scripturl. '?action=rpg_cal',
 		'name' => $txt['rpg_cal'],
	);

		$subActions = array(
		'year' => 'rpgCalYear',
		'bbcode' => 'rpgCalBBCode',
	);

  $_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'year';
  
  $subActions[$_REQUEST['sa']]();
}


function rpgCalCalendar($startdate,$enddate) {

  $eventslegend=array();
  $eventmultiple=array(0,255);
  if ($startdate[1] !== $enddate[1]) {
    if ($startdate[0] == $enddate[0]) {
      echo '<h4 class="rpgCal-dates-header">'. date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&ndash;'. date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
      $i = intval($startdate[1]);
      while ($i <= $enddate[1]):
        list($events,$legend, $eventmultiple)=rpgCalEvent($startdate[0],$i, $eventmultiple);
        echo rpgCalDrawCalendar($i,$startdate[0],$events);
        $eventslegend=$eventslegend+$legend;
        $i++;
      endwhile;
    }
    else {
      echo '<h4 class="rpgCal-dates-header">'. date("F j, Y", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&ndash;'. date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
      $i = intval($startdate[1]);
      while ($i <= 12):
        list($events,$legend, $eventmultiple)=rpgCalEvent($startdate[0],$i, $eventmultiple);
        echo rpgCalDrawCalendar($i,$startdate[0],$events);
        $eventslegend=$events+$eventslegend;
        $i++;
      endwhile;
      $i=1;
      while ($i <= $enddate[1]):
        list($events,$legend, $eventmultiple)=rpgCalEvent($enddate[0],$i, $eventmultiple);
        echo rpgCalDrawCalendar($i,$enddate[0],$events);
        $eventslegend=$eventslegend+$legend;
        $i++;
      endwhile;
    }
  }
  else {
    echo '<h4 class="rpgCal-dates-header">'. date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&dash;'. date("j Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
    list($events,$eventslegend,$eventmultiple)=rpgCalEvent($enddate[0],$enddate[1], $eventmultiple);
    echo rpgCalDrawCalendar($enddate[1],$enddate[0],$events);
  }
  $legend=super_unique($eventslegend);
  echo rpgCalLegend($legend);
  global $context;
}

function super_unique($array)
{
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

  foreach ($result as $key => $value)
  {
    if ( is_array($value) )
    {
      $result[$key] = super_unique($value);
    }
  }

  return $result;
}
/* draws a calendar */
function rpgCalDrawCalendar($month,$year,$events){
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="rpgCal">';

	/* table headings */
	$headings = array('Su','Mo','Tu','We','Th','Fr','Sa');
	$calendar.= '<caption>'.date("F", mktime(0, 0, 0, $month, 01, $year)).'</caption>'. PHP_EOL .'<tr class="rpgCal-row">'. PHP_EOL .'<th class="rpgCal-day-head">'.implode('</th>'. PHP_EOL .'<th class="rpgCal-day-head">',$headings).'</th>'. PHP_EOL .'</tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();
	/* row for week one */
	$calendar.= '<tr class="rpgCal-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="rpgCal-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		if (isset($events[$list_day])):
			$calendar.= '<td class="rpgCal-day tip" data-tip="'.$events[$list_day]['title'].'" style="color:'.$events[$list_day]['color'].'">'.$list_day.'</td>'.PHP_EOL;
			else:
				$calendar.= '<td class="rpgCal-day">'.$list_day.'</td>'.PHP_EOL;
		endif;
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="rpgCal-row">'.PHP_EOL;
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="rpgCal-day-np"> </td>'.PHP_EOL;
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>'.PHP_EOL;

	/* end the table */
	$calendar.= '</table>'.PHP_EOL;
	
	/* all done, return result */
	return $calendar;
}
function rpgCalEvent($year,$month,$multiple){
global $smcFunc, $db_prefix, $modSettings;
	$request = $smcFunc['db_query']('', '
		SELECT DAYOFMONTH(event_date) AS day, title, color
		FROM {db_prefix}rpg_cal_events
		WHERE (YEAR(event_date)={int:year} OR YEAR(event_date)=0004) AND MONTH(event_date)={int:month}',
		array(
			'year' => intval($year),
			'month' => intval($month),
		)
	);
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
		if (isset($events[$row['day']])) {
			$events[$row['day']]['title'] .= ', '. $row['title'];
			switch ($multiple[0]) {
				case 0:
					$events[$row['day']]['color'] = rgb2hex(array($multiple[1],120,100));
					$multiple[0]++;
					break;
				case 1:
					$events[$row['day']]['color'] = rgb2hex(array(120,$multiple[1],100));
					$multiple[0]++;
					break;
				case 2:
					$events[$row['day']]['color'] = rgb2hex(array(120,100,$multiple[1]));
					$multiple[0]=0;
					break;
			}
			$multiple[1]-=50;
			$legend[$year .'-'.$month.'-'.$row['day']]['title'].= ', '. $row['title'];
			$legend[$year .'-'.$month.'-'.$row['day']]['color']=$events[$row['day']]['color'];
		}
		else {
			$events[$row['day']] = array(
				'title' => $row['title'],
				'color' => $row['color'],
			);
			$legend[$year .'-'.$month.'-'.$row['day']]=array (
				'title' => $row['title'],
				'color' => $row['color'],
			);
		}
	$smcFunc['db_free_result']($request);
	if (empty($events)) {
	$events['0']=array(
		'title'=>'',
		'color'=> '',
		);
	$legend['0000-00-00']=&$events['0'];
	}
	
	return array($events, $legend, $multiple);
}

function rpgCalEventDisplay($year,$month){
global $smcFunc, $db_prefix, $modSettings;
	$request = $smcFunc['db_query']('', '
		SELECT id_event, YEAR(event_date) AS year, MONTH(event_date) AS month, DAYOFMONTH(event_date) AS day, title, color
		FROM {db_prefix}rpg_cal_events
		WHERE (YEAR(event_date)={int:year} OR YEAR(event_date)=0004) AND MONTH(event_date)={int:month}',
		array(
			'year' => $year,
			'month' => $month,
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$events[$row['day']] = array(
			'id' => $row['id_event'],
			'day' => $row['day'],
			'month' => $row['month'],
			'year' => $row['year'] <= 4 ? 0 : $row['year'],
			'title' => $row['title'],
			'color' => $row['color']
		);
	$smcFunc['db_free_result']($request);
		
	echo'<pre>'; print_r($events); echo '<pre/>';
	var_dump($events);
}

function rpgCalLegend($events) {
	if (!isset($events['0000-00-00'])){
		$legend='<ul class="rpgCal-legend">';
		foreach ($events as $key => $value) {
			$legend.= '<li class="rpgCal-legend-item" style="color:'.$value['color'].'">'.$value['title'].'</li>'.PHP_EOL;
		}
		$legend.='</ul>';
		return $legend;
	}
}

function rgb2hex($rgb) {
   $hex = "#";
   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

   return $hex; // returns the hex value including the number sign (#)
}

/* draws a calendar */
function rpgCalDrawBBCode($month,$year,$events){
	global $txt;
	/* draw table */
	$calendar = '<pre>[quote][center][b]'.date("F", mktime(0, 0, 0, $month, 01, $year)).'[/b][/center][pre]'. PHP_EOL;

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
	$calendar.= PHP_EOL .'[/pre][/quote]</pre>'.PHP_EOL;
	
	/* all done, return result */
	return $calendar;
}

function rpgCalBBCodeContent($year,$month) {
	$eventMultiple=array(0,255);
	list($events,$legend, $eventmultiple)=rpgCalEvent($year,$month, $eventMultiple);
	$content['calendar']=rpgCalDrawBBCode($month,$year,$events);
	ksort($events);
	if (!isset($events['0'])){
		$content['legend']='<ul>';
			foreach ($events as $key => $value) {
				$content['legend'].= '<li style="color:'.$value['color'].'">[color='.$value['color'].'][b]'.date("F", mktime(0, 0, 0, $month, 01, $year)).' '.$key.'-'.$value['title'].'[/b][/color]</li>'.PHP_EOL;
			}
			$content['legend'].='</ul>';
	}
	else {$content['legend']='';}
	return $content;

}

function rpgCalYear() {
echo 'future feature';
}
?>