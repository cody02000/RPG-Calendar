<?php
/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.2
* @license BSD 3-clause
*/

// Editing or adding holidays.
function template_edit_event()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Start with javascript for getting the calendar dates right.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

			function generateDays()
			{
				var days = 0, selected = 0;
				var dayElement = document.getElementById("day"), yearElement = document.getElementById("year"), monthElement = document.getElementById("month");

				monthLength[1] = 28;
				if (yearElement.options[yearElement.selectedIndex].value % 4 == 0)
					monthLength[1] = 29;

				selected = dayElement.selectedIndex;
				while (dayElement.options.length)
					dayElement.options[0] = null;

				days = monthLength[monthElement.value - 1];

				for (i = 1; i <= days; i++)
					dayElement.options[dayElement.length] = new Option(i, i);

				if (selected < days)
					dayElement.selectedIndex = selected;
			}
		// ]]></script>';

	// Show a form for all the holiday information.
	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=rpg_cal;sa=edit_event" method="post" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl class="settings">
						<dt class="small_caption">
							<strong>', $txt['rpg_cal_title_label'], ':</strong>
						</dt>
						<dd class="small_caption">
							<input type="text" name="title" value="', $context['rpg_cal_event']['title'], '" size="55" maxlength="60" />
						</dd>
						<dt class="small_caption">
							<strong>', $txt['rpg_cal_color'], ':</strong>
						</dt>
						<dd class="small_caption">
							<input type="text" name="color" value="', $context['rpg_cal_event']['color'], '" size="55" maxlength="60" />
						</dd>
						<dt class="small_caption">
							<strong>', $txt['rpg_cal_calendar_year'], '</strong>
						</dt>
						<dd class="small_caption">
							<select name="year" id="year" onchange="generateDays();">
								<option value="0000"', $context['rpg_cal_event']['year'] == '0000' ? ' selected="selected"' : '', '>', $txt['rpg_cal_every_year'], '</option>';
	// Show a list of all the years we allow...
	for ($year = $modSettings['cal_minyear']; $year <= $modSettings['cal_maxyear']; $year++)
		echo '
								<option value="', $year, '"', $year == $context['rpg_cal_event']['year'] ? ' selected="selected"' : '', '>', $year, '</option>';

	echo '
							</select>&nbsp;
							', $txt['rpg_cal_calendar_month'], '&nbsp;
							<select name="month" id="month" onchange="generateDays();">';

	// There are 12 months per year - ensure that they all get listed.
	for ($month = 1; $month <= 12; $month++)
		echo '
								<option value="', $month, '"', $month == $context['rpg_cal_event']['month'] ? ' selected="selected"' : '', '>', $txt['months'][$month], '</option>';

	echo '
							</select>&nbsp;
							', $txt['rpg_cal_calendar_day'], '&nbsp;
							<select name="day" id="day" onchange="generateDays();">';

	// This prints out all the days in the current month - this changes dynamically as we switch months.
	for ($day = 1; $day <= $context['rpg_cal_event']['last_day']; $day++)
		echo '
								<option value="', $day, '"', $day == $context['rpg_cal_event']['day'] ? ' selected="selected"' : '', '>', $day, '</option>';

	echo '
							</select>
						</dd>
					</dl>';

	if ($context['is_new'])
		echo '
					<input type="submit" value="', $txt['rpg_cal_button_add'], '" class="button_submit" />';
	else
		echo '
					<input type="submit" name="edit" value="', $txt['rpg_cal_button_edit'], '" class="button_submit" />
					<input type="submit" name="delete" value="', $txt['rpg_cal_button_remove'], '" class="button_submit" />
					<input type="hidden" name="event" value="', $context['rpg_cal_event']['id'], '" />';
	echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</form>
	</div>
	<br class="clear" />';
}

function template_BBCode()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings,$sourcedir;
	$rpgDate=explode('-',$modSettings['rpg_start_date']);
		// Show a form for all the holiday information.
	echo '
	<div id="admincenter">
	<div id="rpgCal-BBCodedisplay">';
	
	if (isset($context['rpg_cal_bbcode']))
		echo $context['rpg_cal_bbcode']['calendar'] . $context['rpg_cal_bbcode']['legend'];
	echo '</div>

			<div class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
				<form action="',$context['rpg_cal_form'],'" method="post" accept-charset="', $context['character_set'], '">
					<dl class="settings">
						<dt class="small_caption">
							<strong>', $txt['rpg_cal_bbcode_date'], '</strong>
						</dt>
						<dd class="small_caption">', $txt['rpg_cal_calendar_year'], '
							<select name="rpgCal-year" id="rpgCal-year">';
	// Show a list of all the years we allow...STARTDATE
	for ($i=-5; $i <= 5; $i++)
		echo '
								<option value="', $rpgDate[0]+$i, '" ',(($i==0) ? 'selected': '' ),'>', $rpgDate[0]+$i, '</option>';

	echo '
							</select>&nbsp;
							', $txt['rpg_cal_calendar_month'], '&nbsp;
							<select name="rpgCal-month" id="rpgCal-month">';

	// There are 12 months per year - ensure that they all get listed.
	for ($month = 1; $month <= 12; $month++)
		echo '
								<option value="', $month, '" ',(($rpgDate[1]==$month)? 'selected':''),'>', $txt['months'][$month], '</option>';

	echo '
							</select>
						</dd>
					</dl>';

	echo '
					<input type="submit" name="submit" value="',$txt['rpg_cal_submit_bbcode'], '" accesskey="p" class="button_submit" />
				</form></div>
				<span class="botslice"><span></span></span>
			</div>
	</div>
	</div>';
	
/*			echo '
	<script type="text/javascript"><!-- // --><![CDATA[';

	echo '
		function rpgCalBBCode()
			{
			var rpgCalYear=$("#rpgCal-year").val();
			var rpgCalMonth=$("#rpgCal-month").val();

			var parameters="rpgCalYear="+rpgCalYear+"&rpgCalMonth="+rpgCalMonth;

			$.post( ',$sourcedir,'/rpgCalBBCode.php", parameters,function( data ) {
				$("#rpgCal-BBCodedisplay").html(data);} , "html" );
			}


		// ]]></script>';

	*/

}

function template_general_settings()
{	
	//	Show the confiq_vars.
	template_show_settings();
	
	//	Put in a spacer to make it look better.
	echo '
	<br />';

	//	Show the list.
	template_show_list();

}

function template_current()
{
	
}