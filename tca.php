<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_sklinklist_categories"] = Array (
	"ctrl" => $TCA["tx_sklinklist_categories"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,fe_group,category,subcategory"
	),
	"feInterface" => $TCA["tx_sklinklist_categories"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"category" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_categories.category",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"subcategory" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_categories.subcategory",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_sklinklist_categories",	
				"foreign_table_where" => "AND tx_sklinklist_categories.pid=###CURRENT_PID### ORDER BY tx_sklinklist_categories.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, category, subcategory")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "fe_group")
	)
);



$TCA["tx_sklinklist_links"] = Array (
	"ctrl" => $TCA["tx_sklinklist_links"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,url,description,label,category,rating"
	),
	"feInterface" => $TCA["tx_sklinklist_links"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"url" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links.url",		
			"config" => Array (
				"type" => "input",		
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"description" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links.description",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"label" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links.label",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"category" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links.category",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_sklinklist_categories",	
				"foreign_table_where" => "AND tx_sklinklist_categories.pid=###CURRENT_PID### ORDER BY tx_sklinklist_categories.uid",	
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 5,
			)
		),
		"rating" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_linklist/locallang_db.php:tx_sklinklist_links.rating",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, url, description, label, category, rating")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>