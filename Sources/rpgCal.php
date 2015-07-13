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
		'current' => 'rpgCalCurrent',
		'year' => 'rpgCalYear',
		'bbcode' => 'rpgCalBBCode',
	);

  $_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'current';
  
  $subActions[$_REQUEST['sa']]();
}

function rpgCalCurrent() {
	global $context, $scripturl, $txt, $modSettings;
	$context['sub_template'] = 'current';
	
	$type = 'full_calendar';
	$context['rpgCal']=true;
	$startdate = explode("-", empty($start) ? $modSettings['rpg_start_date']:$start);
	$enddate = explode("-", empty($end) ? $modSettings['rpg_end_date']:$end);

	$eventslegend=array();
	$eventmultiple=array(0,255);
	if ($startdate[1] !== $enddate[1]) {
		if ($startdate[0] == $enddate[0]) {
			$context['rpg_full_calendar']['start_date'] = date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0]));
			$context['rpg_full_calendar']['end_date'] = date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0]));
			$i = intval($startdate[1]);
			while ($i <= $enddate[1]):
				list($events,$legend, $eventmultiple)=rpgCal_event($startdate[0],$i, $eventmultiple);
				$context['rpg_full_calendar']['calendar'][$startdate[0]][date("F", mktime(0, 0, 0, $i, 01, $startdate[0]))]=  rpgCal_drawCalendar($i,$startdate[0],$events,$type);
				$eventslegend=$eventslegend+$legend;
				$i++;
			endwhile;
		}
		else {
			$context['rpg_full_calendar']['start_date'] = date("F j, Y", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0]));
			$context['rpg_full_calendar']['end_date'] = date("F j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0]));
			$i = intval($startdate[1]);
			while ($i <= 12):
				list($events,$legend, $eventmultiple)=rpgCal_event($startdate[0],$i, $eventmultiple);
				$context['rpg_full_calendar']['calendar'] .=  rpgCal_drawCalendar($i,$startdate[0],$events,$type);
				$eventslegend=$events+$eventslegend;
				$i++;
			endwhile;
			$i=1;
			while ($i <= $enddate[1]):
				list($events,$legend, $eventmultiple)=rpgCal_event($enddate[0],$i, $eventmultiple);
				$context['rpg_full_calendar']['calendar'] .=  rpgCal_drawCalendar($i,$enddate[0],$events,$type);
				$eventslegend=$eventslegend+$legend;
				$i++;
			endwhile;
		}
	}
	else {
		$context['rpg_full_calendar']['start_date'] = date("F j", mktime(0, 0, 0, $startdate[1], $startdate[2], $startdate[0]));
		$context['rpg_full_calendar']['end_date'] = date("j, Y", mktime(0, 0, 0, $enddate[1], $enddate[2], $enddate[0]));
		list($events,$eventslegend,$eventmultiple)=rpgCal_event($enddate[0],$enddate[1], $eventmultiple);
		$context['rpg_full_calendar']['calendar'] =  rpgCal_drawCalendar($enddate[1],$enddate[0],$events,$type);
	}
	$legend=super_unique($eventslegend);
	$context['rpg_full_calendar']['legend'] =  rpgCal_legend($legend);
	var_dump($context);
}

function rpgCalYear() {
	global $context, $scripturl, $txt;
	$context['sub_template'] = 'year';
	$context['rpgCalTest'] = 'Full Year Calendar Future Feature';
}

function rpgCalBBCode() {
	global $context, $scripturl, $txt;
	$context['sub_template'] = 'bbcode';
	$context['rpgCalTest'] = 'BBCode Calendar Future Feature';
}
?>