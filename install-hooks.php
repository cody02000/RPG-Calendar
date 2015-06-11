<?php
/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.2
* @license BSD 3-clause
*/

// If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
  require_once(dirname(__FILE__) . '/SSI.php');
// Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
  die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
  
global $smcFunc, $db_prefix, $modSettings, $sourcedir, $boarddir, $settings, $db_package_log, $package_cache;

// Define the hooks
$hook_functions = array(
  'integrate_pre_include' => '$sourcedir/Subs-rpgCal.php',
  'integrate_actions' => 'rpg_cal',
  'integrate_load_theme' => 'rpgCalHeader',
  'integrate_admin_areas' => 'rpgCalAdminMenu',
  'integrate_menu_buttons' => 'rpgCalMenuButton',
  'integrate_load_permissions' => 'rpgCalPermissions',
);
// Adding or removing them?
if (!empty($context['uninstalling']))
  $call = 'remove_integration_function';
else
  $call = 'add_integration_function';
// Do the deed
foreach ($hook_functions as $hook => $function)
  $call($hook, $function);
  
if (!empty($context['uninstalling']) && isset($modSettings['sp_version'])) {
	require_once($sourcedir . '/Subs-PortalAdmin.php');
	$request = $smcFunc['db_query']('',
	'SELECT `id_block`
	FROM  `smf_sp_blocks` 
	WHERE  `type` LIKE {text:block_type}',
	array(
		'block_type' => 'sp_rpgCal',
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request)) {


		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}sp_blocks
			WHERE id_block = {int:id}',
			array(
				'id' => $row['id_block'],
			)
		);

		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}sp_parameters
			WHERE id_block = {int:id}',
			array(
				'id' => $row['id_block'],
			)
		);
		fixColumnRows($row['col']);
	}
	
	  // Insert simple portal function.
	$smcFunc['db_query']('','
		DELETE FROM {db_prefix}sp_functions
		WHERE name = {string:name}',
		array(
			'name' => 'sp_rpgCal',
		)
	);
}
?>