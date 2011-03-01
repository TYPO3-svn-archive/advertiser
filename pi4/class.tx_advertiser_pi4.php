<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ulfried Herrmann <herrmann.at.die-netzmacher.de>
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
 * Plugin 'Advertiser: Switch to single view' for the 'advertiser' extension.
 * Used if advertisement records are located in different sysfolders and have different single view (dependend on location)
 * Also used for handling display status by advertisers (front end users when logged in); see setStatus()
 *
 * @author	Ulfried Herrmann <herrmann.at.die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_advertiser
 */
class tx_advertiser_pi4 extends tslib_pibase {
	var $prefixId      = 'tx_advertiser_pi4';                // Same as class name
	var $scriptRelPath = 'pi4/class.tx_advertiser_pi4.php';  // Path to this script relative to the extension dir.
	var $extKey        = 'advertiser';                       // The extension key.
	var $extKey_tx     = 'tx_advertiser';                    // The prefixed extension key.

	// -------------------------------------------------------------------------
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
			//  prepare plugin config
		$conf_ext   = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['advertiser']);
		$this->conf = array_merge($conf, $conf_ext);
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('CONF', $this->prefixId, -1, $this->conf);
		}

			//	current record: get from piVars of plugin: tx_browser_pi1
		$currentRecord = t3lib_div::_GP('tx_browser_pi1');
		if (empty ($currentRecord) AND !empty ($_SESSION['tx_advertiser_pi3']['recordUid'])) {
			$currentRecord['showUid'] = $_SESSION['tx_advertiser_pi3']['recordUid'];
		}
		$currentRecord['showUid'] = (int)$currentRecord['showUid'];
		    //  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current record is: ' . $currentRecord['showUid'], $this->prefixId, -1);
		}
			//	set status? (when handling display status)
		if (!empty ($this->piVars['setstatus'])) {
			$targetPid = $this->setStatus($currentRecord);
			$urlParameters = array(
			    'tx_browser_pi1[mode]' => $currentRecord['mode'],
			);
		} else {
			$targetPid = $this->getSingleViewPid($currentRecord);
			$urlParameters = array(
			    'tx_browser_pi1[showUid]' => $currentRecord['showUid'],
			);
		}

		if (!empty ($targetPid)) {
				//	target was found -> redirect
			$id       =& $targetPid;
			$target   = '';
			$location = $this->pi_getPageLink($id, $target, $urlParameters);
			$location = t3lib_div::locationHeaderUrl($location);
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('Current record\'s target:' . $location, $this->prefixId, -1, $urlParameters);
			}
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $location);
			exit;
		}


			//  in case of failure:
		$content = $this->pi_getLL('errorNoSingleView');

		return '<div class="tx-arj-4">' . $content . '</div>';
	}


	// -------------------------------------------------------------------------
	/**
	 * search for pid of current record and return the appropriate single view page
	 *
	 * @return	void
	 */
	function getSingleViewPid($currentRecord) {
	    $targetPid = 0;  //  fall back

			//	get pid and type of current record:
		$select_fields = 'pid, type';
		$from_table    = $this->extKey_tx . '_ads';
		$where_clause  = 'uid = ' . (int)$currentRecord['showUid'];
		$limit         = '0, 1';
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy = '', $orderBy = '', $limit);
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current record info: QUERY' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy = '', $orderBy = '', $limit);
		$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

			//  compare result with ts conf:
			//  target pid depends on record pid and record type
		$redirect =& $this->conf['redirect.'][$ftc['pid'] . '.'];
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current pid info: ' . $GLOBALS['TSFE']->id, $this->prefixId, -1);
		}
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current conf info', $this->prefixId, -1, $this->conf);
		}
		if (!empty ($redirect[$ftc['type']])) {
				//  both pid and type is configured
			$targetPid = $redirect[$ftc['type']];
		} elseif (!empty ($redirect[0])) {
				//  type is empty, try type = 0 instead of
			$targetPid = $redirect[0];
		} else {
				//  nothing configured: target pid empty
			$targetPid = $this->conf[$ftc['pid']][0];
		}
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current targetPid info: ' . $targetPid, $this->prefixId, -1);
		}

		return ($targetPid);
	}


	// -------------------------------------------------------------------------
	/**
	 * Update display settings for current record: set new start-/ endttime
	 *
	 * @return	void
	 */
	function setStatus($currentRecord) {
		$targetPid = $this->conf['backPid'];

		switch ($this->piVars['setstatus']) {
			case 'publish':
					//  ### future :TODO: here invoice processing ###
					//  set startime -> now/ endtime -> starttime + dwelltime
				$fields_values = array(
				    'starttime' => time(),
					'endtime'   => time() + ($this->conf['dwelltime'] * 24 * 60 * 60),
				);
				break;
			case 'unpublish':
				//  set endtime --> now
				$fields_values = array(
					'endtime'   => time() - 60,
				);
				break;
		}

			//  update in db
		$table           = $this->extKey_tx . '_ads';
		$where           = 'uid = ' . (int)$currentRecord['showUid'];
		$no_quote_fields = false;
		$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current record update: QUERY' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
			//	log
		if (!empty($err) AND $this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
			t3lib_div::devlog('Current record update: ERROR' . $err, $this->prefixId, 3);
		}

		return ($targetPid);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi4/class.tx_advertiser_pi4.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi4/class.tx_advertiser_pi4.php']);
}
?>