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
	global $context, $scripturl, $txt;
	$context['sub_template'] = 'current';
	$context['rpgCalTest'] = 'Current Calendar Future Feature';
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