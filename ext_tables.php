<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$tempColumns = Array (
	"tx_sklinklist_view" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.0", "0"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.1", "1"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.2", "2"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.3", "3"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.4", "4"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.5", "5"),
				Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.tx_sklinklist_view.I.6", "6"),
			),
		)
	),
);


t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);

$TCA["tx_sklinklist_categories"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_categories",		
		"label" => "category",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_sklinklist_categories.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, fe_group, category, subcategory",
	)
);

$TCA["tx_sklinklist_links"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links",		
		"label" => "url",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY url",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_sklinklist_links.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, url, description, label, category, rating",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
$TCA["tt_content"]["types"]["list"]["subtypes_addlist"][$_EXTKEY."_pi1"]="tx_sklinklist_view;;;;1-1-1";


t3lib_extMgm::addPlugin(Array("LLL:EXT:sk_linklist/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_sklinklist_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_sklinklist_pi1_wizicon.php";
?>