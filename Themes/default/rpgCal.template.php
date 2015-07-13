<?php
/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.3
* @license BSD 3-clause
*/

function template_current()
{
	global $context, $scripturl, $txt, $modSettings;
	
	echo '<div class="cat_bar"><h3 class="catbg">'. $context['rpg_full_calendar']['start_date'] . '&ndash;'. $context['rpg_full_calendar']['end_date'] .'</h3></div>
	
	<div class=windowbg>';
	var_dump($context['rpg_full_calendar']['calendar']); 
	echo $context['rpg_full_calendar']['legend'];
	echo '</div>';
}

function template_year()
{
	global $context, $scripturl, $txt, $modSettings;
	echo $context['rpgCalTest'];
	echo '<h1>year</h1>';
}

function template_bbcode()
{
	global $context, $scripturl, $txt, $modSettings;
	echo $context['rpgCalTest'];
	echo '<h1>bbcode</h1>';
}