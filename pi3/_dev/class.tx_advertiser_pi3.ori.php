<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ulfried Herrmann <herrmann@die-netzmacher.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Ads: Data edit' for the 'advertiser' extension.
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_advertiser
 */
class tx_advertiser_pi3 extends tslib_pibase {
	var $prefixId      = 'tx_advertiser_pi3';		// Same as class name
	var $scriptRelPath = 'pi3/class.tx_advertiser_pi3.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'advertiser';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content, $conf)	{
		switch((string)$conf['CMD'])	{
			case 'singleView':
				list($t) = explode(':',$this->cObj->currentRecord);
				$this->internal['currentTable']=$t;
				$this->internal['currentRow']=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content, $conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,'tt_content'))	{
					$conf['pidList'] = $this->cObj->data['pages'];
					$conf['recursive'] = $this->cObj->data['recursive'];
				}
				return $this->pi_wrapInBaseClass($this->listView($content, $conf));
			break;
		}
	}
	
	/**
	 * Shows a list of database entries
	 *
	 * @param	string		$content: content of the PlugIn
	 * @param	array		$conf: PlugIn Configuration
	 * @return	HTML list of table entries
	 */
	function listView($content, $conf) {
		$this->conf = $conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		
		$lConf = $this->conf['listView.'];	// Local settings for the listView function
	
		if ($this->piVars['showUid'])	{	// If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_advertiser_ads';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_advertiser_ads',$this->piVars['showUid']);
	
			$content = $this->singleView($content, $conf);
			return $content;
		} else {
			$items=array(
				'1'=> $this->pi_getLL('list_mode_1','Mode 1'),
				'2'=> $this->pi_getLL('list_mode_2','Mode 2'),
				'3'=> $this->pi_getLL('list_mode_3','Mode 3'),
			);
			if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
			if (!isset($this->piVars['mode']))	$this->piVars['mode']=1;
	
				// Initializing the query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,3);		// Number of results to show in a listing.
			$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal['searchFieldList']='title,description,age,warranty,price,shipping';
			$this->internal['orderByList']='uid,title,age,warranty,price,shipping';
	
				// Get number of records:
			$res = $this->pi_exec_query('tx_advertiser_ads',1);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
	
				// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query('tx_advertiser_ads');
			$this->internal['currentTable'] = 'tx_advertiser_ads';
	
				// Put the whole list together:
			$fullTable='';	// Clear var;
		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
	
				// Adds the mode selector.
			$fullTable.=$this->pi_list_modeSelector($items);
	
				// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);
	
				// Adds the search box:
			$fullTable.=$this->pi_list_searchBox();
	
				// Adds the result browser:
			$fullTable.=$this->pi_list_browseresults();
	
				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * Display a single item from the database
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	HTML of a single database entry
	 */
	function singleView($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
	
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];
	
		$content='<div'.$this->pi_classParam('singleView').'>
			<H2>Record "'.$this->internal['currentRow']['uid'].'" from table "'.$this->internal['currentTable'].'":</H2>
				<p'.$this->pi_classParam("singleViewField-type").'><strong>'.$this->getFieldHeader('type').':</strong> '.$this->getFieldContent('type').'</p>
				<p'.$this->pi_classParam("singleViewField-product-group").'><strong>'.$this->getFieldHeader('product_group').':</strong> '.$this->getFieldContent('product_group').'</p>
				<p'.$this->pi_classParam("singleViewField-manufacturer").'><strong>'.$this->getFieldHeader('manufacturer').':</strong> '.$this->getFieldContent('manufacturer').'</p>
				<p'.$this->pi_classParam("singleViewField-title").'><strong>'.$this->getFieldHeader('title').':</strong> '.$this->getFieldContent('title').'</p>
				'.$this->getFieldContent('description').'
				<p'.$this->pi_classParam("singleViewField-category").'><strong>'.$this->getFieldHeader('category').':</strong> '.$this->getFieldContent('category').'</p>
				<p'.$this->pi_classParam("singleViewField-state").'><strong>'.$this->getFieldHeader('state').':</strong> '.$this->getFieldContent('state').'</p>
				<p'.$this->pi_classParam("singleViewField-age").'><strong>'.$this->getFieldHeader('age').':</strong> '.$this->getFieldContent('age').'</p>
				<p'.$this->pi_classParam("singleViewField-warranty").'><strong>'.$this->getFieldHeader('warranty').':</strong> '.$this->getFieldContent('warranty').'</p>
				<p'.$this->pi_classParam("singleViewField-image").'><strong>'.$this->getFieldHeader('image').':</strong> '.$this->getFieldContent('image').'</p>
				<p'.$this->pi_classParam("singleViewField-price").'><strong>'.$this->getFieldHeader('price').':</strong> '.$this->getFieldContent('price').'</p>
				<p'.$this->pi_classParam("singleViewField-vat").'><strong>'.$this->getFieldHeader('vat').':</strong> '.$this->getFieldContent('vat').'</p>
				<p'.$this->pi_classParam("singleViewField-price-mode").'><strong>'.$this->getFieldHeader('price_option').':</strong> '.$this->getFieldContent('price_option').'</p>
				<p'.$this->pi_classParam("singleViewField-shipping").'><strong>'.$this->getFieldHeader('shipping').':</strong> '.$this->getFieldContent('shipping').'</p>
				<p'.$this->pi_classParam("singleViewField-dispatch").'><strong>'.$this->getFieldHeader('dispatch').':</strong> '.$this->getFieldContent('dispatch').'</p>
				<p'.$this->pi_classParam("singleViewField-payment").'><strong>'.$this->getFieldHeader('payment').':</strong> '.$this->getFieldContent('payment').'</p>
				<p'.$this->pi_classParam("singleViewField-advertiser").'><strong>'.$this->getFieldHeader('advertiser').':</strong> '.$this->getFieldContent('advertiser').'</p>
				<p'.$this->pi_classParam("singleViewField-premium").'><strong>'.$this->getFieldHeader('premium').':</strong> '.$this->getFieldContent('premium').'</p>
				<p'.$this->pi_classParam("singleViewField-sold").'><strong>'.$this->getFieldHeader('sold').':</strong> '.$this->getFieldContent('sold').'</p>
		<p>'.$this->pi_list_linkSingle($this->pi_getLL('back','Back'),0).'</p></div>'.
		$this->pi_getEditPanel();
	
		return $content;
	}
	/**
	 * Returns a single table row for list view
	 *
	 * @param	integer		$c: Counter for odd / even behavior
	 * @return	A HTML table row
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel='<TD>'.$editPanel.'</TD>';
	
		return '<tr'.($c%2 ? $this->pi_classParam('listrow-odd') : '').'>
				<td><p>'.$this->getFieldContent('uid').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('type').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('product_group').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('manufacturer').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('title').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('category').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('state').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('age').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('warranty').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('image').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('price').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('vat').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('price_option').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('shipping').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('dispatch').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('payment').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('advertiser').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('premium').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('sold').'</p></td>
			</tr>';
	}
	/**
	 * Returns a table row with column names of the table
	 *
	 * @return	A HTML table row
	 */
	function pi_list_header()	{
		return '<tr'.$this->pi_classParam('listrow-header').'>
				<td><p>'.$this->getFieldHeader_sortLink('uid').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('type').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('product_group').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('manufacturer').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('title').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('category').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('state').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('age').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('warranty').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('image').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('price').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('vat').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('price_option').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('shipping').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('dispatch').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('payment').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('advertiser').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('premium').'</p></td>
				<td nowrap><p>'.$this->getFieldHeader('sold').'</p></td>
			</tr>';
	}
	/**
	 * Returns the content of a given field
	 *
	 * @param	string		$fN: name of table field
	 * @return	Value of the field
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case 'uid':
				return $this->pi_list_linkSingle($this->internal['currentRow'][$fN],$this->internal['currentRow']['uid'],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			break;
			case "title":
					// This will wrap the title in a link.
				return $this->pi_list_linkSingle($this->internal['currentRow']['title'],$this->internal['currentRow']['uid'],1);
			break;
			case "description":
				return $this->pi_RTEcssText($this->internal['currentRow']['description']);
			break;
			default:
				return $this->internal['currentRow'][$fN];
			break;
		}
	}
	/**
	 * Returns the label for a fieldname from local language array
	 *
	 * @param	[type]		$fN: ...
	 * @return	[type]		...
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			case "title":
				return $this->pi_getLL('listFieldHeader_title','<em>title</em>');
			break;
			default:
				return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}
	
	/**
	 * Returns a sorting link for a column header
	 *
	 * @param	string		$fN: Fieldname
	 * @return	The fieldlabel wrapped in link that contains sorting vars
	 */
	function getFieldHeader_sortLink($fN)	{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array('sort'=>$fN.':'.($this->internal['descFlag']?0:1)));
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi3/class.tx_advertiser_pi3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi3/class.tx_advertiser_pi3.php']);
}

?>
