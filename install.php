<?php
/**
* @package RPG Date and Calendar Mod
*
* @author Cody Williams
* @copyright 2015
* @version 1.2
* @license BSD 3-clause
*/

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
	

global $smcFunc, $db_prefix, $modSettings, $sourcedir, $boarddir, $settings, $db_package_log, $package_cache;

date_default_timezone_set('GMT');
$defaultdate=date('Y-m-d');
$nextdate = date('Y-m-d',(mktime(0, 0, 0, date("m")+1, date("d"),   date("Y"))));

  $defaults = array(
    'rpg_start_date'=> $defaultdate,
    'rpg_end_date'=> $nextdate,
  );
  
  $updates = array(
    'rpg_cal_version' => '1.2',
  );
  
  foreach ($defaults as $index => $value)
    if (!isset($modSettings[$index]))
      $updates[$index] = $value;
  
  updateSettings($updates);
  
  if (isset($modSettings['sp_version'])) {
    
  // Insert simple portal function.
  $smcFunc['db_insert']('ignore',
    '{db_prefix}sp_functions',
    array(
      'id_function' => 'int',
      'function_order' => 'int',
      'name' => 'string',
    ),
    array(
      array(127, 101, 'sp_rpgCal'),
    ),
    array('id_function')
  );
  
  }
	
	$tables = array(
	'rpg_cal_events' => array(
		'columns' => array(
			array(
				'name' => 'id_event',
				'type' => 'int',
				'size' => '5',
				'unsigned' =>true,
				'auto' => true,
			),
			array(
				'name' => 'event_date',
				'type' => 'date',
				'default' => "0001-01-01",
			),
			array(
				'name' => 'title',
				'type' => 'varchar',
				'size' => '60',
			),
			array(
				'name' => 'color',
				'type' => 'varchar',
				'size' => '60',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_event'),
			),
		),
	),


);

// Loop through each table and do what needed.
foreach ($tables as $table => $data)
{

		$smcFunc['db_create_table']('{db_prefix}' . $table, $data['columns'], $data['indexes'], array(), 'ignore');

}
	
$smcFunc['db_insert']('replace',
	'{db_prefix}rpg_cal_events',
	array(
		'id_event' => 'int', 'event_date' => 'date', 'title' => 'string','color' => 'string',
	),
	array(
		array(1,'2010-01-30','Full Moon','orange'),
		array(2,'2010-02-28','Full Moon','orange'),
		array(3,'2010-03-30','Full Moon','orange'),
		array(4,'2010-04-28','Full Moon','orange'),
		array(5,'2010-05-27','Full Moon','orange'),
		array(6,'2010-06-26','Full Moon','orange'),
		array(7,'2010-07-26','Full Moon','orange'),
		array(8,'2010-08-24','Full Moon','orange'),
		array(9,'2010-09-23','Full Moon','orange'),
		array(10,'2010-10-23','Full Moon','orange'),
		array(11,'2010-11-21','Full Moon','orange'),
		array(12,'2010-12-21','Full Moon','orange'),
		array(13,'2011-01-19','Full Moon','orange'),
		array(14,'2011-02-18','Full Moon','orange'),
		array(15,'2011-03-19','Full Moon','orange'),
		array(16,'2011-04-18','Full Moon','orange'),
		array(17,'2011-05-17','Full Moon','orange'),
		array(18,'2011-06-15','Full Moon','orange'),
		array(19,'2011-07-15','Full Moon','orange'),
		array(20,'2011-08-13','Full Moon','orange'),
		array(21,'2011-09-12','Full Moon','orange'),
		array(22,'2011-10-12','Full Moon','orange'),
		array(23,'2011-11-10','Full Moon','orange'),
		array(24,'2011-12-10','Full Moon','orange'),
		array(25,'2012-01-09','Full Moon','orange'),
		array(26,'2012-02-07','Full Moon','orange'),
		array(27,'2012-03-08','Full Moon','orange'),
		array(28,'2012-04-06','Full Moon','orange'),
		array(29,'2012-05-06','Full Moon','orange'),
		array(30,'2012-06-04','Full Moon','orange'),
		array(31,'2012-07-03','Full Moon','orange'),
		array(32,'2012-08-02','Full Moon','orange'),
		array(33,'2012-08-31','Blue Moon','DeepSkyBlue'),
		array(34,'2012-09-30','Full Moon','orange'),
		array(35,'2012-10-29','Full Moon','orange'),
		array(36,'2012-11-28','Full Moon','orange'),
		array(37,'2012-12-28','Full Moon','orange'),
		array(38,'2013-01-27','Full Moon','orange'),
		array(39,'2013-02-25','Full Moon','orange'),
		array(40,'2013-03-27','Full Moon','orange'),
		array(41,'2013-04-25','Full Moon','orange'),
		array(42,'2013-05-25','Full Moon','orange'),
		array(43,'2013-06-23','Full Moon','orange'),
		array(44,'2013-07-22','Full Moon','orange'),
		array(45,'2013-08-21','Full Moon','orange'),
		array(46,'2013-09-19','Full Moon','orange'),
		array(47,'2013-10-18','Full Moon','orange'),
		array(48,'2013-11-17','Full Moon','orange'),
		array(49,'2013-12-17','Full Moon','orange'),
		array(50,'2014-01-16','Full Moon','orange'),
		array(51,'2014-02-14','Full Moon','orange'),
		array(52,'2014-03-16','Full Moon','orange'),
		array(53,'2014-04-15','Full Moon','orange'),
		array(54,'2014-05-14','Full Moon','orange'),
		array(55,'2014-06-13','Full Moon','orange'),
		array(56,'2014-07-12','Full Moon','orange'),
		array(57,'2014-08-10','Full Moon','orange'),
		array(58,'2014-09-09','Full Moon','orange'),
		array(59,'2014-10-08','Full Moon','orange'),
		array(60,'2014-11-06','Full Moon','orange'),
		array(61,'2014-12-06','Full Moon','orange'),
		array(62,'0004-01-01','New Year\'s Day','purple'),
		array(63,'0004-02-14','Valentine\'s Day','hotpink'),
		array(64,'0004-03-01','St. David\'s Day','white'),
		array(65,'0004-03-17','St Patricks Day','green'),
		array(66,'0004-04-23','St. George\'s Day','red'),
		array(67,'0004-10-31','Halloween','red'),
		array(68,'0004-11-05','Guy Fawkes Day','green'),
		array(69,'0004-11-30','St Andrew\'s Day','DarkTurquoise'),
		array(70,'0004-12-24','Christmas Eve','red'),
		array(71,'0004-12-25','Christmas Day','green'),
		array(72,'0004-12-26','Boxing Day','BurlyWood'),
		array(73,'0004-12-31','New Year\'s Eve','yellow'),
		array(74,'2010-03-20','Equinox','Khaki'),	
		array(75,'2010-06-21','Solstice','BlueViolet'),
		array(76,'2010-09-23','Equinox','Khaki'),
		array(77,'2010-12-21','Solstice','BlueViolet'),
		array(78,'2011-03-20','Equinox','Khaki'),
		array(79,'2011-06-21','Solstice','BlueViolet'),
		array(80,'2011-09-23','Equinox','Khaki'),
		array(81,'2011-12-22','Solstice','BlueViolet'),
		array(82,'2012-03-20','Equinox','Khaki'),
		array(83,'2012-06-20','Solstice','BlueViolet'),
		array(84,'2012-09-22','Equinox','Khaki'),
		array(85,'2012-12-21','Solstice','BlueViolet'),
		array(86,'2013-03-20','Equinox','Khaki'),
		array(87,'2013-06-21','Solstice','BlueViolet'),
		array(88,'2013-09-22','Equinox','Khaki'),
		array(89,'2013-12-21','Solstice','BlueViolet'),
		array(90,'2014-03-20','Equinox','Khaki'),
		array(91,'2014-06-21','Solstice','BlueViolet'),
		array(92,'2014-09-23','Equinox','Khaki'),
		array(93,'2014-12-21','Solstice','BlueViolet'),
		array(94,'2015-03-20','Equinox','Khaki'),
		array(95,'2015-06-21','Solstice','BlueViolet'),
		array(96,'2015-09-23','Equinox','Khaki'),
		array(97,'2015-12-22','Solstice','BlueViolet'),
		array(98,'2016-03-20','Equinox','Khaki'),
		array(99,'2016-06-20','Solstice','BlueViolet'),
		array(100,'2016-09-22','Equinox','Khaki'),
		array(101,'2016-12-21','Solstice','BlueViolet'),
		array(102,'2017-03-20','Equinox','Khaki'),
		array(103,'2017-06-21','Solstice','BlueViolet'),
		array(104,'2017-09-22','Equinox','Khaki'),
		array(105,'2017-12-21','Solstice','BlueViolet'),
		array(106,'2018-03-20','Equinox','Khaki'),
		array(107,'2018-06-21','Solstice','BlueViolet'),
		array(108,'2018-09-23','Equinox','Khaki'),
		array(109,'2018-12-21','Solstice','BlueViolet'),
		array(110,'2019-03-20','Equinox','Khaki'),
		array(111,'2019-06-21','Solstice','BlueViolet'),
		array(112,'2019-09-23','Equinox','Khaki'),
		array(113,'2019-12-22','Solstice','BlueViolet'),
		array(114,'2020-03-20','Equinox','Khaki'),
		array(115,'2020-06-20','Solstice','BlueViolet'),
		array(116,'2020-09-22','Equinox','Khaki'),
		array(117,'2020-12-21','Solstice','BlueViolet'),
	),
	array('id_event')
	);
?>