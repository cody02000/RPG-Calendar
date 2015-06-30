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

/**
 *  Define the function and file used for the rpg_cal action.  
 *  
 *  Called by the integrate_actions hook.
 *  
 *  @param array $actionArray
 */
function rpg_cal(&$actionArray)
{
	$actionArray['rpg_cal'] = array('rpgCal.php', 'rpgCalMain');
}

/**
 *  Adds jquery, css, and javascript to the $context['html_headers'].  
 *  Sets $context['jquery'] so that other rpg mods will not add jquery a secondd time and defines which mod set it.
 *  
 *  Called by integrate_load_theme hook.
 */
function rpgCal_header(){
  global $context, $settings, $modSettings;
  
    if (!isset($context['jquery'])) {
      $context['html_headers'] .='<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>';
      $context['jquery']='rpgCal';
    }
  
  $context['html_headers'] .= '<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/rpgCal.js?10"></script>
    <link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/rpgCal.css?10" />
    <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
    $(document).ready(function() {$(".tip").tipr();});
    // ]]></script>';
}
/**
 *  Sets up admin areas.
 *  
 *  Called by integrate_admin_areas hook.
 *  
 *  @param array $admin_areas
 */
function rpgCal_adminMenu(&$admin_areas)
{
  global $txt, $scripturl;
  loadLanguage('rpgCal');
  $admin_areas['config']['areas']['rpg_cal']=array(
    'label' => $txt['rpg_cal_label'],
    'file' => 'rpgCalAdmin.php',
    'function' => 'rpgCalAdminMain',
    'custom_url' => $scripturl . '?action=admin;area=rpg_cal;sa=events',
    'icon' => 'calendar.gif',
    'subsections' => array(
      'events' => array($txt['rpg_cal_manage_events'], 'rpg_cal_edit_settings',),
      'bbcode' => array($txt['rpg_cal_bbcode'],'rpg_cal_edit_settings'),
    ),);
}
/**
 *  Sets up permissions to edit permissions.
 *  
 *  Called by the integrate_load_permissions hook.
 *  
 */
function rpgCal_permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
  global $context;
  
  // Add to the non-guest permissions
  $new = array('rpg_cal_edit_settings');
  $context['non_guest_permissions'] = array_merge($context['non_guest_permissions'], $new);
  
  // And the permission list.
  $list = array(
    'rpg_cal_edit_settings' => array(false, 'maintenance', 'administrate'),
  );
  $permissionList['membergroup'] = array_merge($permissionList['membergroup'], $list);
}

/**
 *  Funtion for setting up a call to rpgCalCalendar for a portal block.
 *  
 *  Currently used for Simple Portal.  Could probably be used for other portals as well.
 *  
 *  @param string $start start date in iso format.
 *  @param string $end end date in iso format.
 */
function rpgCal_portalBlock($start=0,$end=0)
{
  global $context, $modSettings,$sourcedir;
  $context['rpgCal']=true;
  $startdate = explode("-", empty($start) ? $modSettings['rpg_start_date']:$start);
  $enddate = explode("-", empty($end) ? $modSettings['rpg_end_date']:$end);
  
  rpgCal_calendar($startdate,$enddate);
}

/**
 *  Creates a full calendar for specified start and end dates with a legend.
 *  
 *  Calls rpgCalEvent to get events.
 *  Calls rpgCalDrawCalendar to create each month calendar.
 *  Calls super_unique to remove any duplicate events from the events array before creating legend
 *  Calls rpgCalLegend to create combined legend for all events.
 */

function rpgCal_calendar($startdate,$enddate) {

  $eventslegend=array();
  $eventmultiple=array(0,255);
  if ($startdate[1] !== $enddate[1]) {
    if ($startdate[0] == $enddate[0]) {
      echo '<h4 class="rpgCal-dates-header">'. date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&ndash;'. date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
      $i = intval($startdate[1]);
      while ($i <= $enddate[1]):
        list($events,$legend, $eventmultiple)=rpgCal_event($startdate[0],$i, $eventmultiple);
        echo rpgCal_drawCalendar($i,$startdate[0],$events);
        $eventslegend=$eventslegend+$legend;
        $i++;
      endwhile;
    }
    else {
      echo '<h4 class="rpgCal-dates-header">'. date("F j, Y", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&ndash;'. date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
      $i = intval($startdate[1]);
      while ($i <= 12):
        list($events,$legend, $eventmultiple)=rpgCal_event($startdate[0],$i, $eventmultiple);
        echo rpgCal_drawCalendar($i,$startdate[0],$events);
        $eventslegend=$events+$eventslegend;
        $i++;
      endwhile;
      $i=1;
      while ($i <= $enddate[1]):
        list($events,$legend, $eventmultiple)=rpgCal_event($enddate[0],$i, $eventmultiple);
        echo rpgCal_drawCalendar($i,$enddate[0],$events);
        $eventslegend=$eventslegend+$legend;
        $i++;
      endwhile;
    }
  }
  else {
    echo '<h4 class="rpgCal-dates-header">'. date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0])) . '&dash;'. date("j Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0])).'</h4>';
    list($events,$eventslegend,$eventmultiple)=rpgCal_event($enddate[0],$enddate[1], $eventmultiple);
    echo rpgCal_drawCalendar($enddate[1],$enddate[0],$events);
  }
  $legend=super_unique($eventslegend);
  echo rpgCal_legend($legend);
  global $context;
}

/**
 *  Create calendar and legend.
 *  
 *  Calls rpgCalDrawCalendar to create calendar for one month.  BBCode form only allows requesting one month at a time.
 *  Calls rpgCalEvent to get events.  Function then creates bbcode legend.
 *  
 *  @param int $year Four digit year
 *  @param int $month Two digit month
 *  
 *  @return string BBCode calendar and legend
 */
function rpgCal_bbcodeContent($year,$month) {
	$eventMultiple=array(0,255);
	list($events,$legend, $eventmultiple)=rpgCal_event($year,$month, $eventMultiple);
	$content['calendar']=rpgCalDrawBBCode($month,$year,$events,'bbcode');
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

/**
 *  Draws a calendar from the given dates with the given events of a certain calendar type.
 *  
 *  @param int $month two digit month
 *  @param int $year four digit year
 *  @param array $events array of events from the specified month.
 *  @param string $type the type of calendar to draw.
 *  
 *  @returns string $parsedCalendar of whatever type of calendar was specified by $type.
 */
function rpgCal_drawCalendar($month,$year,$events,$type='block'){
	global $txt;
	
	//All of the place-holders for different types of calendars.  
	//First level of array is the calendar type, second level are all the place-holders.
	$drawCal = array(
		'bbcode' => array(
			'calendar_open' => '<pre>[quote]',
			'heading_open' => '[center][b]',
			'heading_close' => '[/b][/center][pre]'. PHP_EOL,
			'table_heading_open' => '',
			'table_heading_implode' => ' ',
			'table_heading_close' => PHP_EOL,
			'row_week_one' => '',
			'blank_days' => '   ',
			'event_day_open' => '',
			'event_day_color_open' => '[color=',
			'event_day_color_close' => ']',
			'event_day_close' => '[/color] ',
			'non_day_open' => '',
			'non_day_close' => ' ',
			'week_end' => PHP_EOL,
			'week_start' => '',
			'last_row' => PHP_EOL,
			'calendar_close' => '[/pre][/quote]</pre>'.PHP_EOL,
		),
		'block' => array(
			'calendar_open' => '<table cellpadding="0" cellspacing="0" class="rpgCal-block">' . PHP_EOL,
			'heading_open' => '<caption>',
			'heading_close' => '</caption>'. PHP_EOL,
			'table_heading_open' => '<tr class="rpgCal-block-row">'. PHP_EOL .'<th class="rpgCal-block-day-head">',
			'table_heading_implode' => '</th>'. PHP_EOL .'<th class="rpgCal-block-day-head">',
			'table_heading_close' => '</th>'. PHP_EOL .'</tr>',
			'row_week_one' => '<tr class="rpgCal-block-row">',
			'blank_days' => '<td class="rpgCal-block-day-np"> </td>',
			'event_day_open' => '<td class="rpgCal-block-day tip" data-tip="',
			'event_day_color_open' => '" style="color:',
			'event_day_color_close' => '">',
			'event_day_close' => '</td>'.PHP_EOL,
			'non_day_open' => '<td class="rpgCal-block-day">',
			'non_day_close' => '</td>'.PHP_EOL,
			'week_end' => '</tr>',
			'week_start' => '<tr class="rpgCal-block-row">'.PHP_EOL,
			'last_row' => '</tr>',
			'calendar_close' => '</table>',
		),
	);
	
	
	//Start new calendar month.
	$calendar='{calendar_open}';
	$monthTitle=date("F", mktime(0, 0, 0, $month, 01, $year));
	$calendar.='{heading_open}' . $monthTitle . '{heading_close}';
	
	/* table headings */
	$headings = $txt['rpg_cal_day_short_headings'];
	$calendar.= '{table_heading_open}' . implode('{table_heading_implode}',$headings) . '{table_heading_close}';
	
	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();
	
	/* row for week one */
	$calendar.= '{row_week_one}';
	
	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '{blank_days}';
		$days_in_this_week++;
	endfor;
	
	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		if ($list_day<=9):
			$calendar.=' ';
		endif;
		if (isset($events[$list_day])):
			$calendar.= '{event_day_open}{event_day_color_open}'.$events[$list_day]['color'].'{event_day_color_close}'.$list_day.'{event_day_close}';
			else:
				$calendar.= '{non_day_open}' . $list_day . '{non_day_close}';
		endif;
		if($running_day == 6):
			$calendar.= '{week_end}';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '{week_start}';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;
	
	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '{blank_days}';
		endfor;
	endif;

	/* final row */
	$calendar.= '{last_row}'.PHP_EOL;

	/* end the table */
	$calendar.= '{calendar_close}'.PHP_EOL;
	
	//parse the calendar.
	$placeholders=$drawCal[$type];
	
	$parsedCalendar = preg_replace_callback(

		// the pattern, no need to escape curly brackets
		// uses a group (the parentheses) that will be captured in $matches[ 1 ]
		'/{([a-z0-9_]+)}/',

		// the callback, uses $placeholders array of possible variables
		function( $matches ) use ( $placeholders )
		{
			$key = $matches[ 1 ];
			// return the complete match (captures in $matches[ 0 ]) if no allowed value is found
			return array_key_exists( $key, $placeholders ) ? $placeholders[ $key ] : $matches[ 0 ];
		},

		// the input string
		$calendar
	);
	
	//return the finished parsed calendar.
	return $parsedCalendar;
}

/**
 *  Queries the database to retrieve all the events for a given month.
 *  
 *  Color is set in the database but is overridden by $multiple when there are two or more events on a date.
 *  Multiple also keeps track to give different colors to multiple event days.
 *  
 *  @param string $year Four digit year.
 *  @param string $month Two digit month.
 *  @param int $multiple
 *  
 *  @return array
 */
function rpgCal_event($year,$month,$multiple){
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

/**
 *  Called by rpgCalCalendar to create legend.
 *  @param array $events with no duplicates.
 *  
 *  @return string $legend HTML output.
 */
function rpgCal_legend($events) {
	if (!isset($events['0000-00-00'])){
		$legend='<ul class="rpgCal-legend">';
		foreach ($events as $key => $value) {
			$legend.= '<li class="rpgCal-legend-item" style="color:'.$value['color'].'">'.$value['title'].'</li>'.PHP_EOL;
		}
		$legend.='</ul>';
		return $legend;
	}
}

/**
 *  Converts rgb value to hex value.
 *  
 *  Called in rpgCalEvents.
 *  
 *  @param array $rgb array of rgb numbers
 *  @return string $hex hex value with # for bbcode and html color.
 */
function rgb2hex($rgb) {
   $hex = "#";
   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

   return $hex; // returns the hex value including the number sign (#)
}

/**
 *  Remove duplicates from multi-dimensional array.
 *  
 *  @param array $array
 *  @return array $result array without duplicates.
 */
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

/**
 *  Var dump all the events for a given month.  Used for debugging.
 */
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