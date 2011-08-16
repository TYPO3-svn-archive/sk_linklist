<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Sebastian Kurfuerst (sebastian@garbage-group.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Link list' for the 'sk_linklist' extension.
 *
 * @author Sebastian Kurfuerst <sebastian@garbage-group.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('lz_table').'class.tx_lztable.php');


class tx_sklinklist_pi1 extends tslib_pibase {
	var $prefixId = 'tx_sklinklist_pi1';  // Same as class name
	var $scriptRelPath = 'pi1/class.tx_sklinklist_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'sk_linklist'; // The extension key.

	var $categories;
	var $links;
	var $sortedCategories;
	var $categories_uidKey;
	/**
	* [Put your description here]
	*/
	function main($content,$conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$GLOBALS['TSFE']->set_no_cache(); // We can't cache because new links have to be show when they are inserted
		$this->build_categorys_and_links(); // Arrays mit Links und Kategorien erzeugen


		$this->buildCategoryTree(0,0);
		switch($this->cObj->data['tx_sklinklist_view'])
		{
			case 0: $content = $this->link_output(); break; // output only the links
			case 1: $content = $this->outputCompleteTree();break; // output complete and expanded tree
			case 2: $content = $this->currentLayer(); break; // output only actual layer
			case 3: $content = $this->search(); break; // output search field
			case 4: $content = $this->outputAddEntry(); break; // output insert field
			case 5: $content = $this->linkEditor(); break; // Link editor
			case 6: break; // Category editor
		}

		return $this->pi_wrapInBaseClass($content);
		}

	function authenticateUser($explode, $searched)
	{
		$array_explode = explode(',', $explode);
		foreach($array_explode as $temp)
			if($searched == $temp)
				return 1;
		return 0;
	}
	function linkEditor()	{
		$linkId = t3lib_div::GPvar('editLinkId');
		$entry = $this->link_lookForUid($linkId);

		$id = $GLOBALS['TSFE']->id;

		$url = t3lib_div::GPvar('url');
		if (!empty($url))	{ // when you have clicked "Submit"

			$pid = $this->cObj->data['pages']; // PID of the page

			if($this->conf['addLink.']['showLabelField'] == 1)
				$label = t3lib_div::GPvar('url_label');
			else
				$label = '';
			$category = t3lib_div::GPvar('category');

			$category_composed = implode(',',$category);

			$updateFields = Array(
				'tstamp' => time(),
				'url' => $url,
				'description' => t3lib_div::GPvar('description'),
				'rating' => t3lib_div::GPvar('rating'),
				'label' => $label,
				'category' => $category_composed
			);

			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_sklinklist_links', 'uid='.intval(t3lib_div::GPvar('uid')), $updateFields);
		} else {
			$out = "<form action=\"".$this->pi_getPageLink($id)."\" method=\"post\"> <input name=\"uid\" type=\"hidden\" value=\"".$linkId."\">
				<table>
				<tr>
					<td>".$this->pi_getLL('url')."</td>
					<td> <input name=\"url\" type=\"text\" size=\"".$this->conf['addLink.']['InputSize.']['URL']."\" value=\"".stripslashes($entry['url'])."\"></td>
				</tr>";
			if($this->conf['addLink.']['showLabelField'] == 1)
				$out .= '<tr><td>'.$this->pi_getLL('label').'</td>
				<td><input name="url_label" type="text" size="'.$this->conf['addLink.']['InputSize.']['Label'].'" value="'.stripslashes($entry['label']).'"></td></tr>';

			$out .= "<tr>
				<td>".$this->pi_getLL('desc')."</td>
				<td><input type=\"text\" size =\"".$this->conf['addLink.']['InputSize.']['Description']."\" name=\"description\" value=\"".stripslashes($entry['description'])."\"></td>
				</tr>
				<tr>
				<td>".$this->pi_getLL('rating')."</td>
				<td><input name=\"rating\" type=\"text\" size=\"".$this->conf['addLink.']['InputSize.']['Rating']."\" maxlength=\"5\" value=\"".stripslashes($entry['rating'])."\"></td>
				</tr>
				<tr>
				<td>".$this->pi_getLL('cat')."</td>
				<td> <select name=\"category[]\" size=\"".$this->conf['addLink.']['InputSize.']['Category']."\" multiple=\"multiple\">";

			$array_selected_categories = explode(",", $entry['category']);

			foreach($this->sortedCategories as $level => $content)
			{
				$level_array = explode(".", $level);
				$count = count($level_array) - 2;
				$temp = "";
				for($i = 0; $i < $count; $i++)
					$temp .= "-";

				$selected = 0;
				foreach($array_selected_categories as $temp1)
					if($temp1 == $content['uid'])
						$selected = 1;
				if($selected == 1)
					$select_yes = " selected=\"selected\"";
				else
					$select_yes = "";
				$out .= "<option value=\"".$content['uid']."\"".$select_yes.">".$temp.$content['category']."</option>";
			}
			$out .= "</select></td></table><input type=\"submit\" value=\"".$this->pi_getLL('submit')."\"></form>";
		}
		return $out;
	}

	function link_lookForUid($uid)	{
		for ($i = 0; isset($this->links[$i]['uid']); $i++)	{
			if ($this->links[$i]['uid'] == $uid)	{
				return $this->links[$i];
			}
		}
	}

	function search()	{
		$out = '';

		$search = t3lib_div::GPvar('tx_sklinklist_pi1_search');

		$out .= $this->pi_getLL('search')."<form action=\"".$this->pi_getPageLink($GLOBALS['TSFE']->id)."?tx_sklinklist_pi1_dummy=".time()."\" method=\"post\"><input name=\"tx_sklinklist_pi1_search\" type=\"text\"><input type=\"submit\" value=\"".$this->pi_getLL('submit')."\"></form>";
		if(!empty($search))
		{
			$i = 0;

			for($a = 0; isset($this->links[$a]); $a++)
			{
				if(stristr($this->links[$a]['url'], $search) || stristr($this->links[$a]['description'], $search))
				{
					$links[$i] = $this->links[$a];
					$i++;
				}
			}


			if($i > 0)
				$out .= $this->link_output($links);
			else
				$out .= $this->pi_getLL('error_notfound');
		}
		return $out;
	}



	/**
	* Outputs the current Layer of the links
	* Not done yet, don't know if it is so good.
	*/
	function currentLayer()
	{
		$out = '';

		$parentLayer = t3lib_div::GPvar('category');
		$conf = $this->conf['showCurrentLayer.'];
		if (empty($parentLayer))	{
			$parentLayer = 0;
		}
		$subcategories = $this->selectSubCategories($parentLayer);


		$out .= $conf['wrap.']['in'];
		if ($parentLayer != 0)	{

			for ($i = 0; isset($this->categories[$i]); $i++)	{
				if ($this->categories[$i]['uid'] == $parentLayer)	{
					$id = $i;
				}
			}
			$this->piVars['category'] = $this->categories[$id]['subcategory'];
			$out .= $conf['elementWrap.']['in'];
			$out .= $this->pi_linkTP($this->pi_getLL('layerUp'),$this->piVars,1);
			$out .= $conf['elementWrap.']['out'];
		}
		if (!empty($subcategories))	{
			foreach ($subcategories as $key=>$value)	{
				$out .= $conf['elementWrap.']['in'];
				$this->piVars['category'] = $value['uid'];
				$out .= $this->pi_linkTP($value['category'],$this->piVars,1);
				$out .= $conf['elementWrap.']['out'];
			}
		}
		$out .= $conf['wrap.']['out'];
		return $out;
	}

	/**
	* Outputs the Add Entry field
	*/
	function outputAddEntry()
	{

		if (isset($_GET['submitted_url']))	{
			$submitted_url = $_GET['submitted_url'];
		} else {
			$submitted_url = $HTTP_GET_VARS['submitted_url'];
		}

		if (isset($_GET['submitted_desc']))	{
			$submitted_desc = $_GET['submitted_desc'];
		} else {
			$submitted_desc = $HTTP_GET_VARS['submitted_desc'];
		}

		$confAddLink = $this->conf['addLink.'];

		$url = t3lib_div::GPvar('tx_sklinklist_pi1_url');
		if (!empty($url))	{ // when you have clicked "Submit"
			$inside = 0;
			$pid = $this->cObj->data['pages'];
			for ($i = 0; isset($this->links[$i]); $i++)	{// check if the url is already inside
				if ($this->links[$i]['url'] == $url)	{
					$out .= $this->pi_getLL('error_url_duplicate');
					return $out; // it is possible to return the values already here
				}
			}
			$category = t3lib_div::GPvar('tx_sklinklist_pi1_category');
			$category_composed = implode(',',$category);


			$label = t3lib_div::GPvar('tx_sklinklist_pi1_label');;

			$insertFields = Array(
				'pid' => $pid,
				'tstamp' => time(),
				'crdate' => time(),
				'cruser_id' => $confAddLink['BEUser'],
				'hidden' => $confAddLink['Hidden'],
				'url' => $url,
				'description' => t3lib_div::GPvar('tx_sklinklist_pi1_description'),
				'category' => $category_composed,
				'rating' => t3lib_div::GPvar('tx_sklinklist_pi1_rating'),
				'label' => $label
			);
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_sklinklist_links', $insertFields);

			if ($GLOBALS['TYPO3_DB']->sql_affected_rows($res))	{
				$out .= $this->pi_getLL('insert_successful');
			}
		} else {
			$id = $GLOBALS['TSFE']->id;

			$out = "<form action=\"".$this->pi_getPageLink($id)."\" method=\"post\"><table>
				<tr><td>".$this->pi_getLL('url')."</td><td> <input name=\"tx_sklinklist_pi1_url\" type=\"text\" size=\"".$confAddLink['InputSize.']['URL']."\" value=\"".$submitted_url."\"></td></tr>";

			if($confAddLink['showLabelField'])
				$out .= '<tr><td>'.$this->pi_getLL('label').'</td><td><input type="text" size="'.$confAddLink['InputSize.']['Label'].'" name="tx_sklinklist_pi1_label"></td></tr>';

			$out .= "<tr><td>".$this->pi_getLL('desc')."</td><td><input type=\"text\" size =\"".$confAddLink['InputSize.']['Description']."\" name=\"tx_sklinklist_pi1_description\" value=\"".$submitted_desc."\"></td></tr>
				<tr><td>".$this->pi_getLL('rating')."</td><td><input name=\"tx_sklinklist_pi1_rating\" type=\"text\" size=\"".$confAddLink['InputSize.']['Rating']."\" maxlength=\"5\"></td></tr>
				<tr><td>".$this->pi_getLL('cat')."</td><td> <select name=\"tx_sklinklist_pi1_category[]\" size=\"".$confAddLink['InputSize.']['Category']."\" multiple=\"multiple\">";

			foreach($this->sortedCategories as $level => $content)
			{
				$level_array = explode(".", $level);
				$count = count($level_array) - 2;
				$temp = "";
				for($i = 0; $i < $count; $i++)
					$temp .= "-";

				$out .= "<option value=\"".$content['uid']."\">".$temp.$content['category']."</option>";
			}
			$out .= "</select></td></table><input type=\"submit\" value=\"".$this->pi_getLL('submit')."\"></form>";
		}
		return $out;
	}
	/**
	* This function outputs the complete tree of the categories
	*/
	function outputCompleteTree()	{
		$oldlevel = "";
		$out = "";
		$conf = $this->conf['showExpandedTree.'];
		if (!empty($this->sortedCategories))	{
			foreach ($this->sortedCategories as $level => $content)	{
				$level_array = explode(".", $level);

				if ($oldlevel != "")	{
					$oldlevel_array = explode(".", $oldlevel);
					$elements_old = count($oldlevel_array);
					$elements = count($level_array);

					$diff = $elements_old - $elements;
					if ($diff > 0)	{
						for ($i = 0; $i < $diff; $i++)	{
							$out .= $conf['wrap.']['out'];
						}
					}
				}

				if ($level_array[count($level_array) - 1] == "0")	{ // if a new hierarchy is beginning
					$out .= $conf['wrap.']['in'];
				}


				$this->piVars['category'] = $content['uid'];
				// BEGINN NEU2
				if ((t3lib_div::GPvar('category') == $content['uid']))
				{
				$out .= $conf['elementWrap.']['in']. $content['category'] ;
				$out .= $conf['elementWrap.']['out'];
				}
				else
				{
				$out .= $conf['elementWrap.']['in'].$this->pi_linkTP($content['category'],$this->piVars,1);
				$out .= $conf['elementWrap.']['out'];
				}

				//$out .= $conf['elementWrap.']['in'].$this->pi_linkTP($content['category'],$this->piVars,1);
				//$out .= $conf['elementWrap.']['out'];
				$oldlevel = $level;
				$count++;
			}

			//HIER IST DIE NEUE STELLE
			for($i = 1; $i < count($level_array); $i++)
				$out .= $conf['wrap.']['out'];
		}

		return $out;
	}

	/**
	* Generates a hierarchical tree out of the array $this->categories into $this->sortedCategories
	*/
	function buildCategoryTree($startLevel, $startId)	{
		$currentLevel = $this->selectSubCategories($startId); // now I have all subcategories in this array
		for($i = 0; $i < count($currentLevel); $i++)
		{
			$levelNumber = $startLevel.'.'.$i;
			$this->sortedCategories[$levelNumber] = $currentLevel[$i];
			$this->buildCategoryTree($levelNumber, $currentLevel[$i]['uid']);
		}
	}

	/**
	* Selects the sub categories of an ID
	*/
	function selectSubCategories($id)	{
		$a = 0;

		for ($i = 0; isset($this->categories[$i]); $i++)	{
			if ($id == $this->categories[$i]['subcategory'])	{
				$filteredValues[$a] = $this->categories[$i];
				$a++;
			}
		}
		return $filteredValues;
	}


	/**
	* Outputs the links as list
	*/
	function link_output($links = 0)	{
		$table = t3lib_div::makeInstance('tx_lztable');
		$category = t3lib_div::GPvar('category');
		if (empty($category) || $category == 0)	{
			$category = NULL;
		}
		if ($links == 0)	{
			$links = $this->select_links($category);
		}
		if (empty($links))	{
			return '';
		}

		$conf_viewStyle = $this->conf['showLinks.']['viewStyle.'][$this->conf['showLinks.']['viewStyle'].'.'];
		$i = 0;
		if ($conf_viewStyle['visible.']['url'] == 1)	{
			$head[$i] = $this->pi_getLL('url');
			$i++;
		}
		if ($conf_viewStyle['visible.']['domain'] == 1)	{
			$head[$i] = $this->pi_getLL('domain');
			$i++;
		}
		if ($conf_viewStyle['visible.']['label'] == 1)	{
			$head[$i] = $this->pi_getLL('label');
			$i++;
		}
		if ($conf_viewStyle['visible.']['description'] == 1)	{
			$head[$i] = $this->pi_getLL('desc');
			$i++;
		}
		if ($conf_viewStyle['visible.']['rating'] == 1)	{
			$head[$i] = $this->pi_getLL('rating');
			$i++;
		}
		if ($conf_viewStyle['visible.']['edit'] == 1)	{
			if($this->authenticateUser($GLOBALS['TSFE']->fe_user->user['usergroup'], $this->conf['linkEditor.']['groupId']) == 1)	{
				$head[$i] = $this->pi_getLL('edit');
				$i++;
			}
		}

		if ($this->conf['showLinks.']['viewStyle.'][$this->conf['showLinks.']['viewStyle'].'.']['visible.']['header'] == 1)	{
			$table->setHeadings($head);
		}
		$table->setBorder($this->conf['showLinks.']['tableBorder']);
		$table->setCellPadding($this->conf['showLinks.']['cellpadding']);
		$table->setTableAlign($this->conf['showLinks.']['tableAlign']);
		$table->setTableStyle($this->conf['showLinks.']['tableStyle']);
		$table->setHeadingStyle($this->conf['showLinks.']['headingStyle']);
		$table->setContentStyle($this->conf['showLinks.']['tableContentStyle']);

		for ($i = 0; isset($links[$i]['uid']); $i++)	{
			$print_link = $links[$i]['url'];
			if ($this->conf['showLinks.']['showLastBackslash'] == 0)	{
				$print_link = preg_replace("=/$=", "", $print_link);
			}
			if ($this->conf['showLinks.']['showHTTP'] == 0)	{
				$print_link = preg_replace("=^http://=", "", $print_link);
			}
			if ($this->conf['showLinks.']['showWWW'] == 0)	{
				$print_link = preg_replace("=^http://www.=", "http://", $print_link);
				$print_link = preg_replace("=^www.=", "", $print_link);
			}

			$laenge_url = strlen($print_link);
			if ($laenge_url > $this->conf['showLinks.']['cutLinks'])	{ // HIER MAXLAENGE REIN
				$print_link = substr($print_link,0,$this->conf['showLinks.']['cutLinks']); //HIER MUSS MAXLAENGE REIN
				$print_link .= $this->conf['showLinks.']['cutLinksAppend'];
			}

			$domain = preg_replace("=^http://=", "", $links[$i]['url']);
			$domain = preg_replace("=^www.=", "", $domain);
			$domain_array = explode("/", $domain);
			$domain = $domain_array[0];

			$label = $links[$i]['label'];

			$laenge_domain = strlen($domain);

			if ($laenge_domain > $this->conf['showLinks.']['cutLinksDomain'])	{ // HIER MAXLAENGE REIN
				$domain = substr($domain,0,$this->conf['showLinks.']['cutLinksDomain']); //HIER MUSS MAXLAENGE REIN
				$domain .= $this->conf['showLinks.']['cutLinksAppend'];
			}

			$a = 0;
			if ($conf_viewStyle['visible.']['url'] == 1)	{
				if ($conf_viewStyle['linked.']['url'] == 1)	{
					$row[$a] = "<a href=\"".stripslashes($links[$i]['url'])."\" target=\"_BLANK\">".stripslashes($print_link)."</a>";
				} else {
					$row[$a] = stripslashes($print_link);
				}
				$a++;
			}
			if ($conf_viewStyle['visible.']['domain'] == 1)	{
				if ($conf_viewStyle['linked.']['domain'] == 1)	{
					$row[$a] = "<a href=\"".stripslashes($links[$i]['url'])."\" target=\"_BLANK\">".stripslashes($domain)."</a>";
				} else {
					$row[$a] = stripslashes($domain);
				}
				$a++;
			}
			if ($conf_viewStyle['visible.']['label'] == 1)	{
				if ($conf_viewStyle['linked.']['label'] == 1)	{
					$row[$a] = "<a href=\"".stripslashes($links[$i]['url'])."\" target=\"_BLANK\">".stripslashes($label)."</a>";
				} else {
					$row[$a] = stripslashes($label);
				}
				$a++;
			}
			if ($conf_viewStyle['visible.']['description'] == 1)	{
				if ($conf_viewStyle['linked.']['description'] == 1)	{
					$row[$a] = "<a href=\"".stripslashes($links[$i]['url'])."\" target=\"_BLANK\">".stripslashes($links[$i]['description'])."</a>";
				} else {
					$row[$a] = stripslashes($links[$i]['description']);
				}
				$a++;
			}
			if ($conf_viewStyle['visible.']['rating'] == 1)	{
				if ($conf_viewStyle['linked.']['rating'] == 1)	{
					$row[$a] = "<a href=\"".stripslashes($links[$i]['url'])."\" target=\"_BLANK\">".stripslashes($links[$i]['rating'])."</a>";
				} else {
					$row[$a] = stripslashes($links[$i]['rating']);
				}
				$a++;
			}
			if ($conf_viewStyle['visible.']['edit'] == 1)	{
				if ($this->authenticateUser($GLOBALS['TSFE']->fe_user->user['usergroup'], $this->conf['linkEditor.']['groupId']) == 1)	{
					$path_to_edit = "typo3/gfx/edit2.gif";
					$this->piVars['editLinkId'] = $links[$i]['uid'];
					$row[$a] = $this->pi_linkToPage("<img src=\"".$path_to_edit."\"", $this->conf['linkEditor.']['editPageId'], $this->conf['linkEditor.']['editPageTarget'], $this->piVars);
					$a++;
				}
			}

			$table->addRow($row);
		}
		return $table->getTable();
	}


	/**
	* builds categories and links arrays
	*
	* they are built into $this->categories and $this->links
	*/
	function build_categorys_and_links()
	{
			// saving of all categories
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_sklinklist_categories',
			'pid='.$this->cObj->data['pages'].$this->cObj->enableFields('tx_sklinklist_categories'),
			'',
			'category'
			);


		$i = 0;
		while ($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$this->categories[$i] = $temp;
			$i++;
			$this->categories_uidKey[$temp['uid']] = 1;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_sklinklist_links',
			'pid='.$this->cObj->data['pages'].$this->cObj->enableFields('tx_sklinklist_links'),
			'',
			'url'
			);

		$i = 0;
		while ($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// filter the links out which don't have a category, in other words, filter out the links in categories where the public access is restricted
			$temp2 = explode(",", $temp['category']);
			$ignore = 1;
			if (!empty($temp2))	{
				foreach ($temp2 as $key => $value)	{
					if ($this->categories_uidKey[$value] == 1)	{
						$ignore = 0;
						break;
					}
				}
			}
			if ($ignore == 0)	{
				$this->links[$i] = $temp;
				$i++;
			}
		}
	}


	/**
	* Selects all links with the given category, only one category is allowed, is used by link_output
	*
	* Returns an array of links.
	*/
	function select_links($category)	{
		$a = 0;
		for ($i = 0; isset($this->links[$i]); $i++)	{
			$temp = explode(',', $this->links[$i]['category']);
			foreach ($temp as $key=>$value)	{
				if ($value == $category)	{
					$links[$a] = $this->links[$i];
					$a++;
				}
			}
		}
		return $links;
	}








}







if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_linklist/pi1/class.tx_sklinklist_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_linklist/pi1/class.tx_sklinklist_pi1.php']);
}

?>
