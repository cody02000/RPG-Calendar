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
  
$txt['rpg_cal_desc'] = 'From here you can add, remove, and edit events for the RPG Calendar and create BBCode calendars.';
$txt['rpg_cal_events_desc']=&$txt['rpg_cal_desc'];
$txt['rpg_cal_label'] = 'RPG Date and Calendar';
$txt['rpg_cal_manage_events'] = 'Manage Events & Dates';
$txt['rpg_start_date'] = 'Begining of Current Date Range';
$txt['rpg_end_date'] = 'End of Current Date Range';
$txt['rpg_cal_color'] = 'Color';
$txt['rpg_cal_bbcode_date'] = 'BBCode Calendar for';
$txt['rpg_cal_bbcode_desc'] = 'Create Calendar with BBCode';
$txt['rpg_cal_bbcode'] = 'BBCode Calendar';
$txt['rpg_cal_calendar_year'] ='Year';
$txt['rpg_cal_calendar_month'] = 'Month';
$txt['rpg_cal_calendar_day'] = 'Day';
$txt['rpg_cal_title_label'] = 'Title';
$txt['rpg_cal_every_year'] = 'Every Year';
$txt['rpg_cal_button_add'] = 'Add';
$txt['rpg_cal_button_edit'] = 'Edit';
$txt['rpg_cal_button_remove'] = 'Remove';
$txt['rpg_cal_submit_bbcode'] = 'Create BBCode Calendar';
$txt['rpg_cal_manage_events_desc'] = 'From here you can add, remove, and edit events on the AO Calendar.';
$txt['rpg_cal_current_events'] = 'Current Events';
$txt['rpg_cal_no_entries'] = 'There are currently no events configured.';
$txt['rpg_cal_event_title'] = 'Event';
$txt['rpg_cal_event_add'] = 'Add New Event';
$txt['rpg_cal_event_edit'] = 'Edit Existing Event';
$txt['rpg_cal_bbcode_title'] = 'Create BBCode Calendar';
$txt['rpg_cal_current_date_settings'] = 'Current Date Settings';
$txt['rpg_cal_page_title']= 'Calendar';
$txt['rpg_cal'] = 'Calendar';

$txt['rpg_cal_short_headings']=array('Su','Mo','Tu','We','Th','Fr','Sa');
$txt['rpg_cal_long_headings']=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

$txt['sp_function_sp_rpgCal_label'] = 'RPG Calendar Block';
$txt['sp_function_sp_rpgCal_desc'] = 'Displays a calendar in a block based on the current dates of the RPG Date and Calendar Mod.';
$txt['sp_param_sp_rpgCal_content_above'] = 'Enter the custom HTML content for above the calendar in this box.';
$txt['sp_param_sp_rpgCal_content_below'] = 'Enter the custom HTML content for below the calendar in this box.';
?>