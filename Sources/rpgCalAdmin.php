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
  


// The main controlling function doesn't have much to do... yet.
function rpgCalAdminMain()
{
	global $context, $txt, $scripturl, $modSettings;
	loadTemplate('rpgCalAdmin');
	loadLanguage('rpgCal');
	
	isAllowedTo('rpg_cal_edit_settings');

	// Default text.
	$context['explain_text'] = $txt['rpg_cal_desc'];

	// Little short on the ground of functions here... but things can and maybe will change...
	$subActions = array(
		'edit_event' => 'rpgCalEditEvent',
		'events' => 'rpgCalEvents',
		'bbcode' => 'rpgCalBBCodeAdmin',
	);

	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'events';

	// Set up the two tabs here...
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['rpg_cal_label'],
		'help' => 'rpg_cal_help',
		'description' => $txt['rpg_cal_events_desc'],
		'tabs' => array(
			'events' => array(
				'description' => $txt['rpg_cal_events_desc'],
			),
			'bbcode' => array(
				'description' => $txt['rpg_cal_bbcode_desc'],
			),
		),
	);

	$subActions[$_REQUEST['sa']]();
}

// The function that handles adding, and deleting holiday data
function rpgCalEvents($return_config = false)
{
	global $sourcedir, $txt, $scripturl, $context, $settings, $sc, $modSettings, $smcFunc;
	require_once($sourcedir . '/ManageServer.php');
	// Submitting something...
	if (isset($_REQUEST['delete']) && !empty($_REQUEST['event']))
	{
		checkSession();

		foreach ($_REQUEST['event'] as $id => $value)
			$_REQUEST['event'][$id] = (int) $id;

		// Now the IDs are "safe" do the delete...
		rpgCalRemoveEvents($_REQUEST['event']);
	}
	else {
		$config_vars = array(
			array('text', 'rpg_start_date', 10),
			array('text', 'rpg_end_date', 10),
		);

		$listOptions = array(
			'id' => 'event_list',
			'title' => $txt['rpg_cal_current_events'],
			'items_per_page' => 20,
			'base_href' => $scripturl . '?action=admin;area=rpg_cal;sa=events',
			'default_sort_col' => 'name',
			'get_items' => array(
				'function' => 'rpgCalListGetEvents',
			),
			'get_count' => array(
				'function' => 'rpgCalListGetNumEvents',
			),
			'no_items_label' => $txt['rpg_cal_no_entries'],
			'columns' => array(
				'name' => array(
					'header' => array(
						'value' => $txt['rpg_cal_event_title'],
					),
					'data' => array(
						'sprintf' => array(
							'format' => '<a href="' . $scripturl . '?action=admin;area=rpg_cal;sa=edit_event;event=%1$d" style="color:%3$s">%2$s</a>',
							'params' => array(
								'id_event' => false,
								'title' => false,
								'color' => false,
							),
						),
					),
					'sort' => array(
						'default' => 'title',
						'reverse' => 'title DESC',
					)
				),
				'date' => array(
					'header' => array(
						'value' => $txt['date'],
					),
					'data' => array(
						'function' => create_function('$rowData', '
							global $txt;

							// Recurring every year or just a single year?
							$year = $rowData[\'year\'] == \'0004\' ? sprintf(\'(%1$s)\', $txt[\'rpg_cal_every_year\']) : $rowData[\'year\'];

							// Construct the date.
							return sprintf(\'%1$d %2$s %3$s\', $rowData[\'day\'], $txt[\'months\'][(int) $rowData[\'month\']], $year);
						'),
						'class' => 'windowbg',
					),
					'sort' => array(
						'default' => 'event_date',
						'reverse' => 'event_date DESC',
					),
				),
				'check' => array(
					'header' => array(
						'value' => '<input type="checkbox" onclick="invertAll(this, this.form);" class="input_check" />',
					),
					'data' => array(
						'sprintf' => array(
							'format' => '<input type="checkbox" name="event[%1$d]" class="input_check" />',
							'params' => array(
								'id_event' => false,
							),
						),
						'style' => 'text-align: center',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=rpg_cal;sa=events',
			),
			'additional_rows' => array(
				array(
					'position' => 'below_table_data',
					'value' => '
						<a href="' . $scripturl . '?action=admin;area=rpg_cal;sa=edit_event" style="margin: 0 1em">[' . $txt['rpg_cal_event_add'] . ']</a>
						<input type="submit" name="delete" value="' . $txt['quickmod_delete_selected'] . '" class="button_submit" />',
					'style' => 'text-align: right;',
				),
			),
		);

		require_once($sourcedir . '/Subs-List.php');
		createList($listOptions);
		
		if ($return_config)
			return $config_vars;
		
		if(isset($_GET['update'])) {
			//	Make sure that an admin is doing the updating.
			checkSession();	
			//	Save the config vars.
			writeLog();
			saveDBSettings($config_vars);
			redirectexit("action=admin;area=rpg_cal;sa=events;");
			}
		
		$context['page_title'] = $txt['rpg_cal_manage_events'];
		//	Set up the variables needed by the template.
		$context['settings_title'] = $txt['rpg_cal_current_date_settings'];	
		$context['page_title'] = $txt['rpg_cal_manage_events'];
		$context['default_list'] = 'event_list';
		$context['post_url'] = $scripturl . '?action=admin;area=rpg_cal;sa=events;update';
		loadTemplate('rpgCalAdmin');
		
		$context['sub_template'] = 'general_settings';
		//	Finally prepare the settings array to be shown by the 'show_settings' template.
		prepareDBSettingContext($config_vars);
	}
}

// This function is used for adding/editing a specific holiday
function rpgCalEditEvent()
{
	global $txt, $context, $scripturl, $smcFunc;

	loadTemplate('rpgCalAdmin');

	$context['is_new'] = !isset($_REQUEST['event']);
	$context['page_title'] = $context['is_new'] ? $txt['rpg_cal_event_add'] : $txt['rpg_cal_event_edit'];
	$context['sub_template'] = 'edit_event';

	// Cast this for safety...
	if (isset($_REQUEST['event']))
		$_REQUEST['event'] = (int) $_REQUEST['event'];

	// Submitting?
	if (isset($_POST[$context['session_var']]) && (isset($_REQUEST['delete']) || $_REQUEST['title'] != ''))
	{
		checkSession();

		// Not too long good sir?
		$_REQUEST['title'] =  $smcFunc['substr']($_REQUEST['title'], 0, 60);
		$_REQUEST['color'] =  $smcFunc['substr']($_REQUEST['color'], 0, 60);
		$_REQUEST['event'] = isset($_REQUEST['event']) ? (int) $_REQUEST['event'] : 0;

		if (isset($_REQUEST['delete']))
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}rpg_cal_events
				WHERE id_event = {int:selected_event}',
				array(
					'selected_event' => $_REQUEST['event'],
				)
			);
		else
		{
			$date = strftime($_REQUEST['year'] <= 4 ? '0004-%m-%d' : '%Y-%m-%d', mktime(0, 0, 0, $_REQUEST['month'], $_REQUEST['day'], $_REQUEST['year']));
			if (isset($_REQUEST['edit']))
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}rpg_cal_events
					SET event_date = {date:event_date}, title = {string:event_title}, color = {string:event_color}
					WHERE id_event = {int:selected_event}',
					array(
						'event_date' => $date,
						'selected_event' => $_REQUEST['event'],
						'event_title' => $_REQUEST['title'],
						'event_color' => $_REQUEST['color'],
					)
				);
			else
				$smcFunc['db_insert']('',
					'{db_prefix}rpg_cal_events',
					array(
						'event_date' => 'date', 'title' => 'string-60','color' => 'string-60',
					),
					array(
						$date, $_REQUEST['title'],$_REQUEST['color'],
					),
					array('id_event')
				);
		}

		updateSettings(array(
			'calendar_updated' => time(),
		));

		redirectexit('action=admin;area=rpg_cal;sa=events');
	}

	// Default states...
	if ($context['is_new'])
		$context['rpg_cal_event'] = array(
			'id' => 0,
			'day' => date('d'),
			'month' => date('m'),
			'year' => '0000',
			'title' => '',
			'color' => ''
		);
	// If it's not new load the data.
	else
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_event, YEAR(event_date) AS year, MONTH(event_date) AS month, DAYOFMONTH(event_date) AS day, title, color
			FROM {db_prefix}rpg_cal_events
			WHERE id_event = {int:selected_event}
			LIMIT 1',
			array(
				'selected_event' => $_REQUEST['event'],
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$context['rpg_cal_event'] = array(
				'id' => $row['id_event'],
				'day' => $row['day'],
				'month' => $row['month'],
				'year' => $row['year'] <= 4 ? 0 : $row['year'],
				'title' => $row['title'],
				'color' => $row['color']
			);
		$smcFunc['db_free_result']($request);
	}

	// Last day for the drop down?
	$context['rpg_cal_event']['last_day'] = (int) strftime('%d', mktime(0, 0, 0, $context['rpg_cal_event']['month'] == 12 ? 1 : $context['rpg_cal_event']['month'] + 1, 0, $context['rpg_cal_event']['month'] == 12 ? $context['rpg_cal_event']['year'] + 1 : $context['rpg_cal_event']['year']));
}

function rpgCalRemoveEvents($event_ids)
{
  global $smcFunc;

  $smcFunc['db_query']('', '
    DELETE FROM {db_prefix}rpg_cal_events
    WHERE id_event IN ({array_int:id_event})',
    array(
      'id_event' => $event_ids,
    )
  );

  updateSettings(array(
    'calendar_updated' => time(),
  ));
  redirectexit("action=admin;area=rpg_cal;sa=events;");
}

function rpgCalListGetEvents($start, $items_per_page, $sort)
{
  global $smcFunc;

  $request = $smcFunc['db_query']('', '
    SELECT id_event, YEAR(event_date) AS year, MONTH(event_date) AS month, DAYOFMONTH(event_date) AS day, title, color
    FROM {db_prefix}rpg_cal_events
    ORDER BY {raw:sort}
    LIMIT ' . $start . ', ' . $items_per_page,
    array(
      'sort' => $sort,
    )
  );
  $events = array();
  while ($row = $smcFunc['db_fetch_assoc']($request))
    $events[] = $row;
  $smcFunc['db_free_result']($request);

  return $events;
}

function rpgCalListGetNumEvents()
{
  global $smcFunc;

  $request = $smcFunc['db_query']('', '
    SELECT COUNT(*)
    FROM {db_prefix}rpg_cal_events',
    array(
    )
  );
  list($num_items) = $smcFunc['db_fetch_row']($request);
  $smcFunc['db_free_result']($request);

  return $num_items;
}

/**
 *  Creates a BBCode calendar for the admin section.
 */
function rpgCalBBCodeAdmin()
{
	global $txt, $context, $scripturl, $sourcedir;

	$context['page_title'] = $txt['rpg_cal_bbcode_title'];
	$context['sub_template'] = 'BBCode';
	$context['rpg_cal_form'] = $scripturl . ($context['current_action']=='admin') ? '?action=admin;area=rpg_cal;sa=bbcode' : '?action=rpg_cal;sa=bbcode';
	
	if (isset($_REQUEST['submit']))
	{
		$year=intval($_REQUEST['rpgCal-year']);
		$month=intval($_REQUEST['rpgCal-month']);
		$context['rpg_cal_bbcode']=rpgCal_bbcodeContent($year,$month);
	}
}
?>