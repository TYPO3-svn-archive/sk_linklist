<?php

########################################################################
# Extension Manager/Repository config file for ext "sk_linklist".
#
# Auto generated 16-08-2011 12:24
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Linklist',
	'description' => 'Linklist with subcategories',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Heiko Westermann / Sebastian Kurfuerst',
	'author_email' => 'hwt3@gmx.de / sebastian@garbage-group.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.8.8',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
			'lz_table' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:12:"ext_icon.gif";s:4:"d958";s:17:"ext_localconf.php";s:4:"b8d9";s:14:"ext_tables.php";s:4:"b893";s:14:"ext_tables.sql";s:4:"27cd";s:28:"ext_typoscript_editorcfg.txt";s:4:"b647";s:24:"ext_typoscript_setup.txt";s:4:"26d9";s:33:"icon_tx_sklinklist_categories.gif";s:4:"6ff8";s:28:"icon_tx_sklinklist_links.gif";s:4:"096d";s:13:"locallang.php";s:4:"16d4";s:16:"locallang_db.php";s:4:"fe5b";s:7:"tca.php";s:4:"c32d";s:14:"doc/manual.sxw";s:4:"5652";s:19:"doc/wizard_form.dat";s:4:"0142";s:20:"doc/wizard_form.html";s:4:"4cdc";s:14:"pi1/ce_wiz.gif";s:4:"4273";s:31:"pi1/class.tx_sklinklist_pi1.php";s:4:"b2c0";s:39:"pi1/class.tx_sklinklist_pi1_wizicon.php";s:4:"1fc3";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"3b32";}',
);

?>