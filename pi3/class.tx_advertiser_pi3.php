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
 * Plugin 'Advertiser: Data edit (form using EXT:Formidable)' for the 'advertiser' extension.
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_advertiser
 *
 * @:TODO: set all needed configuration (like .mail.to) in static template
 */
class tx_advertiser_pi3 extends tslib_pibase {
	var $prefixId      = 'tx_advertiser_pi3';                // Same as class name
	var $scriptRelPath = 'pi3/class.tx_advertiser_pi3.php';  // Path to this script relative to the extension dir.
	var $extKey        = 'advertiser';                       // The extension key.
	var $extKey_tx     = 'tx_advertiser';                    // The prefixed extension key.
	var $pi_checkCHash = true;

	// -------------------------------------------------------------------------
	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content, $conf) {
		$this->pi_loadLL();
			//  prepare plugin config
		$this->prepareConfig($conf);



		@session_start();

			//	set status and redirect back if given
			//	redirect to next step page if given
		if (!empty ($this->piVars['setstatus'])) {
			$currentRecord = t3lib_div::_GP('tx_browser_pi1');
			$id            = $this->setStatus($currentRecord);
			$urlParameters = array(
			    'tx_browser_pi1[mode]' => $currentRecord['mode'],
			);
		} elseif (!empty ($this->piVars['step'])) {
				//	target was found -> redirect
			$id            = $this->getPageId();
			$urlParameters = array();
		}
		if (!empty ($id)) {
			$location = $this->pi_getPageLink($id, $target = '', $urlParameters);
			$location = t3lib_div::locationHeaderUrl($location);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 1) {
				t3lib_div::devlog('REDIRECT: target is ' . $location, $this->prefixId, 1);
			}

			header('Location: ' . $location);
			exit;
		}


			// new record or edit record?
		$editModeResult = $this->getEditMode();
		if (!empty ($editModeResult)) {
				//	log + abort if there is an error message
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('ERROR: ' . $editModeResult, $this->prefixId, 3);
			}
			return ($editModeResult);
		}
			//  log edit mode
		if (!empty ($this->recordUid)) {
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('EDIT mode is active for AD #' . (int)$this->recordUid, $this->prefixId, 0, $this->piVars);
			}
		}

			//  switch: first step (choos ads class) or following step (forms with formidable)?
##		if (empty ($this->piVars['class'])) {
		if ((empty ($this->piVars['class']) OR !isset ($this->piVars['type'])) AND empty ($this->piVars['AMEOSFORMIDABLE_SUBMITTED']) AND empty ($this->recordUid)) {
			unset($_SESSION[$this->prefixId]);
			$content .= $this->getSelectAdsClassForm($content);
			$content  = $this->cObj->stdWrap($content, $this->conf['stdWrap.']['allWrap.']);
##		} elseif (!empty ($this->piVars['class']) AND isset ($this->piVars['type']) AND !empty ($this->piVars['AMEOSFORMIDABLE_SUBMITTED'])) {
		} else {
				//  load Fakt Basic object
			if (!empty ($this->conf['use_fakt_basic']) AND t3lib_extMgm::isLoaded('fakt_basic')) {
				require_once t3lib_extMgm::extPath('fakt_basic') . 'pi1/class.tx_faktbasic_pi1.php';
				$this->faktBasicObj	= t3lib_div::makeInstance('tx_faktbasic_pi1');
				$this->faktBasicLoaded = is_object($this->faktBasicObj);
				$this->faktBasicObj->main('', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_faktbasic_pi1.']);

					//	log
				if (!empty ($this->faktBasicLoaded) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
					t3lib_div::devlog('FAKT loaded', $this->prefixId, 0, array('faktInfo' => print_r($this->faktBasicObj, 1)));
				}
				if (empty ($this->faktBasicLoaded) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
					t3lib_div::devlog('ERROR FAKT loading failed', $this->prefixId, 3);
				}
			};

			$content .= $this->displayAdEditForm();
		}

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * prepare plugin config
	 *
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	void
	 */
	function prepareConfig($conf) {
		$conf['plugin']	=& unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

			//  prepare content record config
		$conf['recursive'] =& $this->cObj->data['recursive'];
		$conf['pidList']   =& $this->cObj->data['pages'];
		$conf['dwelltime'] = !empty ($conf['dwelltime']) ? $conf['dwelltime'] : 30;

		if ($conf['plugin']['useReference'] == 1 OR empty ($conf['formconfigpathupload'])) {
			$conf['formconfigpathupload'] = '';
		}

		$this->conf =& $conf;

			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CONF: is initialized.', $this->prefixId, -1, $this->conf);
		}


			//  read config from flexform and set it to $this->config; if empty use TS config:
			//  Init and get the flexform data of the plugin
		$this->pi_initPIflexForm();
			// Assign the flexform data to a local variable for easier access
		$piFlexForm =& $this->cObj->data['pi_flexform'];
			// Traverse the entire array based on the language and assign each configuration option to $this->lConf array...
		foreach ($piFlexForm['data'] as $sheet => $data ) {
			foreach ($data as $lang => $value) {
				foreach ($value as $key => $val) {
					$this->conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
				}
			}
		}
			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CONF: is extended by flexform.', $this->prefixId, -1, $this->conf);
		}
	}


	// -------------------------------------------------------------------------
	/**
	 * first step (choos ads class): SQL query
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @return	The content that should be displayed on the website
	 */
	function getSelectAdsClassForm($content) {
		if (empty ($this->conf['pidList'])) {
			$_msg = '<pre>CONF ERROR: pidList is empty.</pre>';
			//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog($_msg . ' Set either via TS or via Starting point. Abort.', $this->prefixId, 3, $this->conf);
			}
			return $_msg;
		}

		$currentUser =& $GLOBALS['TSFE']->fe_user->user;

		$select_fields = 'uid, title, fe_group_accesstype, infotext';
		$from_table    = 'tx_advertiser_classes';
		$where_clause  = $GLOBALS['TYPO3_DB']->listQuery('fe_group_access', (int)$currentUser['usergroup'], $from_table);
		$where_clause .= ' AND pid IN (' . $this->conf['pidList']. ')';
		$where_clause .= $this->cObj->enableFields($from_table);
		$groupBy       = '';
		$orderBy       = 'sorting';
		$limit         = '';
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			t3lib_div::devlog('GET allowed ads classes: SQL QUERY: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				//	log + abort
			t3lib_div::devlog('GET allowed ads classes: SQL error! QUERY: ' . $sql . '. ERROR: ' . $err, $this->prefixId, 3);
			return;
		}

		$classes = array();
		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$classes[$ftc['uid']] = array(
				'title'      => $ftc['title'],
				'accesstype' => $ftc['fe_group_accesstype'],
				'infotext'   => $ftc['infotext'],
			);
		}
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('GET allowed ads classes: SQL RESULT', $this->prefixId, -1, $classes);
		}
		if (count ($classes) > 0) {
			$content = $this->displaySelectAdsClassForm($content, $classes);
		}

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * first step (choos ads class): records to HTML template
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @return	The content that should be displayed on the website
	 */
	function displaySelectAdsClassForm($content, &$classes) {
			//  load JS libraries
		$this->loadJSLibraries();

			//  get template file
		$this->templateCode = $this->cObj->fileResource($this->conf['template.']['file']);
		if (empty ($this->templateCode)) {
				//	log + abort
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('TEMPLATE ERROR: no template found.', $this->prefixId, 3);
			}
			return;
		}
		$method = !empty ($this->conf['form.']['method']) ? $this->conf['form.']['method'] : 'get';
		$markerArray = array(
			'###ACTION###'       => $this->pi_getPageLink($id = $GLOBALS['TSFE']->id, $target = '', $urlParameters = array()),
			'###METHOD###'       => strtolower($method),
			'###STEPINFOTEXT###' => $this->pi_getLL('stepinfotext1'),
			'###CLASSHEADER###'  => $this->pi_getLL('classheader'),
			'###SUBMITVALUE###'  => $this->pi_getLL('submitvalue1'),
		);

			// Get whole template
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_CLASSSELECTOR###');

			//  get subtemplates
		$subtemplate = array(
			'listbodyitem'   => $this->cObj->getSubpart($template, '###LISTBODYITEM###'),
			'typeselect'     => $this->cObj->getSubpart($template, '###TYPESELECT###'),
			'typeselectitem' => $this->cObj->getSubpart($template, '###TYPESELECTITEM###'),
			'infotext'       => $this->cObj->getSubpart($template, '###INFOTEXT###'),
		);

			//  loop through class items
		$itemContentArray = array();
		foreach ($classes as $cKey => $cVal) {
			$itemContent = $subtemplate['listbodyitem'];
				//  class title and properties
			$classselectMarkerArray = array(
				'###CLASSID###'     => $cKey,
				'###CLASSTITLE###'  => $cVal['title'],
				'###CHECKED###'     => (!empty ($this->piVars['class']) AND (int)$this->piVars['class'] == $cKey) ? ' checked="checked"' : '',
				'###CONDITIONAL###' => (!empty ($this->piVars['class']) AND (int)$this->piVars['class'] == $cKey) ? '' : ' conditional',
			);

				//  infotext or selector ads type
			if ($cVal['accesstype'] == 'listonly') {
				$infotextContent   = $this->cObj->substituteMarker($subtemplate['infotext'], '###INFOTEXTCONTENT###', $cVal['infotext']);
				$typeselectContent = '';
//				$classselectMarkerArray['###CLASSPROPERTIES###'] = 'disabled="disabled"';
			} else {
				$infotextContent   = '';
				$typeselectContent = '';

					//  define ads types
				$types = array(
					0 => $this->pi_getLL('type.I.0'),  //  bid
					1 => $this->pi_getLL('type.I.1'),  //  request
				);
				if ($cVal['accesstype'] == 'bid') {
					unset($types[1]);  //  no request
				} elseif ($cVal['accesstype'] == 'request') {
					unset($types[0]);  //  no bid
				}

					//  loop through type items
				$typeContentArray = array();
				foreach ($types as $tKey => $tVal) {
					$typeContent = $subtemplate['typeselectitem'];
						//  type title and properties
					$typeselectMarkerArray = array(
						'###CLASSID###'   => $cKey,
						'###TYPEID###'    => $tKey,
						'###TYPETITLE###' => $tVal,
						'###CHECKED###'   => $tVal,
					);
					$typeContent = $this->cObj->substituteMarkerArray($typeContent, $typeselectMarkerArray);

					$typeContentArray[] = $typeContent;
				}
				$typeContent = implode('', $typeContentArray);
				$typeContent = $this->cObj->substituteSubpart($typeContent, '###TYPESELECTITEM###', $typeContent);

				$typeselectContent = $subtemplate['typeselect'];
				$typeselectContent = $this->cObj->substituteMarker($typeselectContent, '###TYPEHEADER###', $this->pi_getLL('typeheader'));
				$typeselectContent = $this->cObj->substituteSubpart($typeselectContent, '###TYPESELECTITEM###', $typeContent);

//				$classselectMarkerArray['###CLASSPROPERTIES###'] = 'onclick="toggleDisplay(' . $cKey . ');"';
			}
			$classselectMarkerArray['###CLASSPROPERTIES###'] = 'onclick="toggleDisplay(' . $cKey . ');"';
			$itemContent = $this->cObj->substituteSubpart($itemContent, '###INFOTEXT###',   $infotextContent);
			$itemContent = $this->cObj->substituteSubpart($itemContent, '###TYPESELECT###', $typeselectContent);
			$itemContent = $this->cObj->substituteMarkerArray($itemContent, $classselectMarkerArray);

			$itemContentArray[] = $itemContent;
		}

		$itemContent = implode('', $itemContentArray);
		$template = $this->cObj->substituteSubpart($template, '###LISTBODYITEM###', $itemContent);
		$template = $this->cObj->substituteMarkerArray($template, $markerArray);
		$content .= $template;

		if (!empty ($this->piVars['class']) AND empty ($this->piVars['type'])) {
			$content .= '
<script type="text/javascript">
	objBlindedDown = \'aj-box_' . (int)$this->piVars['class'] . '\';
;
</script>
';
		}

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * get pid list for current record
	 *
	 * @return	void / string error message
	 */
	function loadJSLibraries() {
			// include js functions
		if (empty ($this->conf['plugin']['includeScriptaculous'])) {
				// log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('Prototype/ scriptaculous library not included. Probably it\'s OK. Change this? Activate option "Include JavaScript libraries" in EM.', $this->prefixId, 0);
			}
		} else {
				//  load prototype / Scriptaculous library
			$_pathPrototype     =& $this->conf['plugin']['pathPrototype'];
			$_pathScriptaculous =& $this->conf['plugin']['pathScriptaculous'];
			if (file_exists(PATH_typo3 . $_pathPrototype)) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '10'] = '<script src="/' . TYPO3_mainDir . $_pathPrototype . '" type="text/javascript"></script>';
			} else {
					// log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 2) {
					t3lib_div::devlog('Error: Prototype library inclusion failed (file [' . PATH_typo3 . $_pathPrototype . '] not found).', $this->prefixId, 2, $_paths);
				}
			}
			@list($_fileScriptaculous, ) = explode('?', $_pathScriptaculous);
			if (file_exists(PATH_typo3 . $_fileScriptaculous)) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '20'] = '<script src="/' . TYPO3_mainDir . $_pathScriptaculous . '" type="text/javascript"></script>';
			} else {
					// log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 2) {
					t3lib_div::devlog('Error: Scriptaculous library inclusion failed (file [' . PATH_typo3 . $_fileScriptaculous . '] not found).', $this->prefixId, 2, $_paths);
				}
			}
				// log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('Prototype / Scriptaculous library included.', $this->prefixId, 0);
			}
		}

			//  load extension related js functions
			//  @ToDo: is it really needed?
		$_localJS =& $this->conf['template.']['jslocal'];
		if (empty ($_localJS)) {
				// log + abort
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('No local javascript included. Probably it\'s OK. Change this? Set typoscript value "plugin.tx_advertiser_pi3.template.jslocal"', $this->prefixId, 0);
			}
			return;
		}
		$_absLocalJS = t3lib_div::getFileAbsFileName($_localJS, true);
		if (!file_exists($_absLocalJS)) {
				// log + abort
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('Error: local js functions file not found! (file: ' . $_absLocalJS, $this->prefixId, 3);
			}
			return;
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '30'] = '<script src="' . $_localJS . '" type="text/javascript"></script>';
			// log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('Local js functions included.', $this->prefixId, 0);
		}
	}


	// -------------------------------------------------------------------------
	/**
	 * detect form template file, include Formidable and form output
	 *
	 * @return	The content that should be displayed on the website
	 */
	function displayAdEditForm() {
		$content = '';

##			// new record or edit record?
##		session_start();
##		$editModeResult = $this->getEditMode();
##		if (!empty ($editModeResult)) {
##				//	log + abort if there is an error message
##			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
##				t3lib_div::devlog('ERROR: ' . $editModeResult, $this->prefixId, 3);
##			}
##			return ($editModeResult);
##		}
##			//  log edit mode
##		if (!empty ($this->recordUid)) {
##			if (!empty ($this->piVars) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
##				t3lib_div::devlog('EDIT mode is active for AD #' . (int)$this->recordUid, $this->prefixId, 0, $this->piVars);
##			}
##		}
			//  log raw form data if any
		if (!empty ($this->piVars) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('PIVARS ' . $editModeResult, $this->prefixId, -1, $this->piVars);
		}

			//  get available credits: on this template file can depend on
		$this->getCreditsAvailable();
		$_SESSION[$this->prefixId]['userCredit'] = $this->userCredit;
			//  get properties of current ads class
		$this->adClassProperties = $this->getAdClassProperties();
		if (!is_file($this->adClassProperties['templatefile'])) {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CONF ERROR: xml config file not found: ' . $this->adClassProperties['templatefile'], $this->prefixId, 3);
			}
			return;
		}
		$_SESSION[$this->prefixId]['adClassProperties'] = $this->adClassProperties;


			//  get pidlist
		$pidListError = $this->getPidList();
		if (!empty ($pidListError)) {
				//	log + abort if there is an error message
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('ERROR: ' . $pidListError, $this->prefixId, 3);
			}
			return ($pidListError);
		}


			/*	display ads data form with Formidable  */
			//  include the Formidable API
		require_once(PATH_formidableapi);
			//  make instance of Formidable
		$this->oForm = t3lib_div::makeInstance('tx_ameosformidable');
		if (is_object($this->oForm)) {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('FORM: object instantiated.', $this->prefixId, -1);
			}
		} else {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('FORM ERROR: object could not be instantiated.', $this->prefixId, 3);
			}
			return;
		}

		if (!empty ($this->recordUid)) {  //  given from $this->getEditMode()
			$this->oForm->init($this, $this->adClassProperties['templatefile'], $this->recordUid);
		} else {
			$this->oForm->init($this, $this->adClassProperties['templatefile']);
		}


			/*  display header part  */
			//  get template file
		$this->templateCode = $this->cObj->fileResource($this->conf['template.']['file']);
		if (empty ($this->templateCode)) {
				//	log + abort
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('TEMPLATE ERROR: no template found.', $this->prefixId, 3);
			}
			return;
		}
		$markerArray = array(
			'###STEPINFOTEXT###'           => $this->pi_getLL('stepinfotext2'),
			'###ADSTYPEDETAILS###'         => sprintf($this->pi_getLL('stepinfotext2title'), $this->adClassProperties['title']),
			'###INFOREQUIREDFIELDS###'     => $this->pi_getLL('inforequiredfields'),
			'###ADSDATAFORM###'            => $this->oForm->render(),
			'###INFONEXTSTEPEDITIMAGES###' => $this->pi_getLL('infonextstepeditimages'),
		);

			// Get whole template
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_ADSDATAFORM###');
		$template = $this->cObj->substituteMarkerArray($template, $markerArray);
		$content .= $template;

##$content .=  '<pre><b><u>$this->adClassProperties:</u></b> ' . print_r($this->adClassProperties, 1) . '</pre>';
##$content .= '<pre><b><u>$GLOBALS[TSFE]->fe_user->user:</u></b> ' . print_r($GLOBALS['TSFE']->fe_user->user, 1) . '</pre>';
##$content .= '<pre><b><u>SESSION:</u></b> ' . print_r($_SESSION[$this->prefixId], 1) . '</pre>';

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * get pid list for current record
	 *
	 * @return	void / string error message
	 */
	function getPidList() {
		$msg      = '';
		$_pidList = null;

		if (!empty ($this->adClassProperties['pages'])) {
			$_pidList   = $this->adClassProperties['pages'];
##			$_recursive = $this->cObj->data['recursive'];
			$_recursive = 0;
		} elseif (!empty ($pidList)) {
			$_pidList   = $pidList;
			$_recursive = 0;
		}
		if (empty ($_pidList)) {
			$_msg = '<pre>CONF ERROR: pidList is empty.</pre>';
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog($_msg . ' Set either via TS or via Starting point. Abort.', $this->prefixId, 3, $this->conf);
			}
		} else {
			$this->conf['pidList'] = $this->pi_getPidList($_pidList, $_recursive);
				//  used in static method tx_advertiser_pi3::getFormData():
			define('TX_ADVERTISER_PI3_PIDLIST', $this->conf['pidList']);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('CONF: TX_ADVERTISER_PI3_PIDLIST is ' . TX_ADVERTISER_PI3_PIDLIST . '.', $this->prefixId, -1, $this->conf);
			}
		}

		return $_msg;
	}


	// -------------------------------------------------------------------------
	/**
	 * get edit mode (create/update) for current record
	 *
	 * @return	void / string error message
	 */
	function getEditMode() {
		$msg           = '';

		$browserPiVars = t3lib_div::_GP('tx_browser_pi1');
		$_adsUid       = null;
		if (!empty ($browserPiVars['showUid'])) {
				//  edit record?
			$_adsUid = (int)$browserPiVars['showUid'];
###	 @ToDo: check if needed!
###		} elseif (!empty ($_SESSION['tx_advertiser_pi3']['recordUid'])) {
###				//	edit images?
###			$_adsUid = (int)$_SESSION['tx_advertiser_pi3']['recordUid'];
		}
			// check permission to edit this record
		if (empty ($_adsUid)) {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('CONF: Edit mode is CREATE.', $this->prefixId, -1);
			}
		} else {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('CONF: Edit mode is EDIT. record: ' . $_adsUid, $this->prefixId, -1);
			}
				// store edit mode
			$this->recordUid = $_adsUid;

				//	ckeck permission
			$select_fields = 'fe_user';
			$from_table    = 'tx_advertiser_ads';
			$where_clause  = 'uid = "' . $_adsUid . '"';
			$groupBy       = '';
			$orderBy       = '';
			$limit         = 1;
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('CHECK permission: QUERY: ' . $sql, $this->prefixId, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$err = $GLOBALS['TYPO3_DB']->sql_error();
				//	log
			if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CHECK permission: SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
			}
			$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if ($ftc['fe_user'] != $GLOBALS['TSFE']->fe_user->user['uid']) {
					//	log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 2) {
					t3lib_div::devlog('PERMISSION restricted: user "' . $GLOBALS['TSFE']->fe_user->user['uid'] . '" tryed to get access for edit images in uid "' . $adsUid . '", but should be "' . $ftc['fe_user'] . '"', $this->prefixId, 2);
				}
				return '<p>Dieser Vorgang ist Ihnen nicht gestattet!</p>';
			}
		}

		return $_msg;
	}

	// -------------------------------------------------------------------------
	/**
	 * ### add desc ###
	 *
	 * @return	array   template properties
	 */
	function getCreditsAvailable() {
			//  get user credits available
		if (!empty ($this->faktBasicLoaded)) {
				##  @ToDo: load faktBasicObj only if necessary
			$this->userCredit = $this->faktBasicObj->getCreditsAvailable($GLOBALS['TSFE']->fe_user->user['uid']);
		}
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('Credits Available: user ' . $GLOBALS['TSFE']->fe_user->user['uid'] . ' can use these creditpoints.', $this->prefixId, 0, $this->userCredit);
		}
	}

	// -------------------------------------------------------------------------
	/**
	 * ### add desc ###
	 *
	 * @return	array   template properties
	 */
	function getAdClassProperties() {
		if (!empty ($_SESSION[$this->prefixId]['adClassProperties']) AND empty ($this->recordUid)) {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('GET records class: from SESSION', $this->prefixId, 0, $_SESSION[$this->prefixId]['adClassProperties']);
			}
			return $_SESSION[$this->prefixId]['adClassProperties'];
		}

			//  if ad is to be edited there's no piVars['class'] - get it from DB
		if (!empty ($this->recordUid)) {
			$select_fields = 'class';
			$from_table    = 'tx_advertiser_ads';
			$where_clause  = 'uid = ' . (int)$this->recordUid;
			$groupBy       = '';
			$orderBy       = '';
			$limit         = '0, 1';
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('GET records class: SQL QUERY: ' . $sql, $this->prefixId, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$err = $GLOBALS['TYPO3_DB']->sql_error();
			if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
					//	log + abort
				t3lib_div::devlog('GET records class: SQL error! QUERY: ' . $sql . '. ERROR: ' . $err, $this->prefixId, 3);
				return;
			}
			$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('GET records class: SQL RESULT', $this->prefixId, -1, $ftc);
			}
			$this->piVars['class'] = $ftc['class'];
		}


		$formConfFile = '';
		$currentUser =& $GLOBALS['TSFE']->fe_user->user;

		$select_fields = 'uid, title, fe_group_access, price, dwelltime, trialperiod, fe_group_accesstype, infotext, infotext_lang_ol, credit_consumption, credittype, storage_pid, vatincluded, vatrate, template_select, templatefile, pages';
		$from_table    = 'tx_advertiser_classes';
		$where_clause  = 'uid = ' . (int)$this->piVars['class'];
		$where_clause .= $this->cObj->enableFields($from_table);
		$groupBy       = '';
		$orderBy       = '';
		$limit         = '0, 1';
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			t3lib_div::devlog('GET template properties: SQL QUERY: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				//	log + abort
			t3lib_div::devlog('GET template properties: SQL error! QUERY: ' . $sql . '. ERROR: ' . $err, $this->prefixId, 3);
			return;
		}
		$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('GET template properties: SQL RESULT', $this->prefixId, -1, $ftc);
		}

			//  form config file from flexform: ad is bid or request?
		if (isset ($this->piVars['type'])) {
			if ((int)$this->piVars['type'] == 0) {
				$field = 'formconfigfilebid';
			} elseif ((int)$this->piVars['type'] == 1) {
				$field = 'formconfigfilerequest';
			}
		} else {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('GET template properties: need type of record', $this->prefixId, 0);
			}

			$select_fields = 'type';
			$from_table    = 'tx_advertiser_ads';
			$where_clause  = 'uid = ' . (int)$this->recordUid;
			$groupBy       = '';
			$orderBy       = '';
			$limit         = '0, 1';
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('GET record type: SQL QUERY: ' . $sql, $this->prefixId, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$err = $GLOBALS['TYPO3_DB']->sql_error();
			if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
					//	log + abort
				t3lib_div::devlog('GET record type: SQL error! QUERY: ' . $sql . '. ERROR: ' . $err, $this->prefixId, 3);
				return;
			}
			$type = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('GET record type: SQL RESULT', $this->prefixId, -1, $type);
			}
			if ((int)$type['type'] == 0) {
				$field = 'formconfigfilebid';
			} elseif ((int)$type['type'] == 1) {
				$field = 'formconfigfilerequest';
			}
		}
			// Converting flexform data into array:
		$flexData     = t3lib_div::xml2array($ftc['templatefile']);
		$formConfFile = $this->pi_getFFvalue($flexData, $field, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF');
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('FORM config file is: ' . $formConfFile, $this->prefixId, -1, $ftc);
		}


		if (empty ($formConfFile)) {
				//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CONF ERROR: no xml config file detected.', $this->prefixId, 3);
			}
		} else {
				//  check file exists and file for edit mode
			if (!file_exists($formConfFile)) {
					//	log + abort
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
					t3lib_div::devlog('CONF ERROR: xml config file not found.', $this->prefixId, 3, array('file' => $formConfFile));
				}
				return FALSE;
			}
				//  need another file for current credit type?
				// convention: form for credit type has the same name, but with ".X" before ".xml". X is the uid of credit type
			$formConfFileCrType = preg_replace('%\.xml$%', '.' . (int)$this->userCredit['type'] . '.xml', $formConfFile);
				// is there a special form for edit mode?
			if (file_exists($formConfFileCrType)) {
				$formConfFile = $formConfFileCrType;
			} else {
					//	log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
					t3lib_div::devlog('CONF ERROR: xml config file for this credittype not found. Use basic form instead - probably it\'s OK. Change this? Create file ' . $formConfFileCrType, $this->prefixId, 0, array('file' => $formConfFile));
				}
			}
### @ToDo: add fall back: edit form without CrType, even if $this->userCredit['type'] is given
				//  need another file for edit mode?
			if (!empty ($this->recordUid)) {
					// convention: form for edit mode has the same name, but with ".edit" before ".xml"
				$formConfFileEdit = preg_replace('%\.xml$%', '.edit.xml', $formConfFile);
					// is there a special form for edit mode?
				if (file_exists($formConfFileEdit)) {
					$formConfFile = $formConfFileEdit;
						//  log
					if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
						t3lib_div::devlog('EDIT mode: FORM config file is: ' . $formConfFile, $this->prefixId, -1, $ftc);
					}
				} else {
						//	log
					if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
						t3lib_div::devlog('CONF ERROR: xml config file for edit mode not found. Use creation form instead - probably it\'s OK. Change this? Create file ' . $formConfFileEdit, $this->prefixId, 0, array('file' => $formConfFile));
					}
				}
			}
				//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('CONF: xml config file detected.', $this->prefixId, 0, array('file' => $formConfFile));
			}
		}

		$ftc['templatefile'] = $formConfFile;

		$adClassProperties = array(
			'uid'                => $ftc['uid'],
			'fe_group_access'    => $ftc['fe_group_access'],
			'title'              => $ftc['title'],
			'price'              => $ftc['price'],
			'dwelltime'          => $ftc['dwelltime'],
			'trialperiod'        => $ftc['trialperiod'],
			'credit_consumption' => $ftc['credit_consumption'],
##			'credittype'         => $ftc['credittype'],
			'storage_pid'        => $ftc['storage_pid'],
			'vatincluded'        => $ftc['vatincluded'],
			'vatrate'            => $ftc['vatrate'],
			'templatefile'       => $ftc['templatefile'],
			'pages'              => $ftc['pages'],
			'useTrial'           => FALSE,
		);

			//  trial?
		if (!empty ($adClassProperties['trialperiod'])) {
			$fe_userCreated =& $GLOBALS['TSFE']->fe_user->user['crdate'];
			$trialPeriodEnd = $fe_userCreated + ($adClassProperties['trialperiod'] * 24 * 60 * 60);
			$currentTime    = time();
			if ($currentTime <= $trialPeriodEnd) {
			    $adClassProperties['useTrial'] = TRUE;
			}

		}

			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
		    $trialLogData = array(
		        'fe_userCreated' => $fe_userCreated,
				'trialPeriodEnd' => $trialPeriodEnd,
				'currentTime'    => $currentTime,
			);
			t3lib_div::devlog('adClassProperties: values.', $this->prefixId, -1, array('properties' => $adClassProperties, 'trial' => $trialLogData));
		}

		return $adClassProperties;
	}


	// -------------------------------------------------------------------------
	/**
	 * Update dispay settings for current record: set new start-/ endttime
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
					//  ### future :TODO: update remaining time ###
				$fields_values = array(
					'endtime'   => time() - 60,
				);
				break;
		}

			//  update in db
		$table           = $this->extKey_tx . '_ads';
		$where           = 'uid = ' . (int)$currentRecord['showUid'];
		$no_quote_fields = false;
			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			t3lib_div::devlog('Current record update: Query: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
			//	log
		if (!empty($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
			t3lib_div::devlog('Current record update: ERROR ' . $err, $this->prefixId, 3);
		}

		return ($targetPid);
	}

	// -------------------------------------------------------------------------
	/**
	 * ### add desc ###
	 * only used to lead from step to step
	 *
	 * @return	void
	 */
	function getPageId() {
			//  lead to next step: user fills in a new ad --> delete former input
		if (!empty ($_SESSION[$this->prefixId]['recordUid'])) {
			unset($_SESSION[$this->prefixId]['recordUid']);
		}

		$pageId = 0;
		$lConf =& $this->conf['redirect.'];

		foreach ($lConf as $cfKey => $cfVal) {
			if (preg_match('%\.$%', $cfKey)) {
				//	exclude 'default' and subvalues like ['n.']
				continue;
			}
			//  :TODO: do it recursive
			$firstKey = $cfKey;
			//  search subkey
		}

		$firstVal = $this->piVars[$firstKey];
 		if (is_array($lConf[$firstKey . '.'][$firstVal . '.'])) {
			foreach ($lConf[$firstKey . '.'][$firstVal . '.'] as $cnKey => $cnVal) {
				$nextKey = preg_replace('%\.$%', '', $cnKey);
			}
			$nextVal = $this->piVars[$nextKey];
			$pageId = $lConf[$firstKey . '.'][$firstVal . '.'][$nextKey . '.'][$nextVal];
		} else {
			$pageId = $lConf[$firstKey . '.'][$firstVal];
		}


		if (empty ($pageId)) {
			//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CONF ERROR: no redirect page id detected.', $this->prefixId, 3);
			}
		} else {
			//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 1) {
				t3lib_div::devlog('CONF: redirect page id detected: ' . $pageId, $this->prefixId, 1);
			}
		}

		return $pageId;
	}

	// -------------------------------------------------------------------------
	/**
	 * ### add desc ###
	 *
	 * @return	void
	 */
	function processData($aData, $step = 1) {
		echo '<pre><b><u>$aData:</u></b> ' . print_r($aData, 1) . '</pre>';
	}

	// -------------------------------------------------------------------------
	/**
	 * Static method for filling form data via formidable
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * if selects/ radios etc. have to get their options from a related table
	 *
	 * @param	string	$case: field to get possible values
	 * @param	bool	$prependEmpty: wether an empty row has to be prepended
	 * @return	array   $data
	 */
	function getFormData($table, $sorting = 'title ASC', $prependEmpty = false, $pid = null, $_hidel10n = false) {
		$data = $prependEmpty === false ? array() : array('x0' => array('caption' => '---', 'value' => ''));
		//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getFormData()\' with parameter table: ' . $table, $this->prefixId, 0);
		}

		$pidList = is_null($pid) ? TX_ADVERTISER_PI3_PIDLIST : $pid;
		//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CALL \'getFormData()\' with pidList: ' . $pidList, $this->prefixId, -1);
		}


		//  prepare query for items:
		### :TODO: translation
		/*
		$_selectl10n   = empty ($_hidel10n) ? 'l10n_parent, ' : '';
		$select_fields = 'uid, ' . $_selectl10n . 'title' . ' AS caption';
		*/
		$select_fields = 'uid, title AS caption';
		$from_table	   =& $table;
		#$_wherel10n    = empty ($_hidel10n) ? 'sys_language_uid = 0 AND ' : '';
		$_wherel10n    = '';
		##$where_clause  = $_wherel10n . 'pid IN (' . $pidList . ')' . tslib_cObj::enableFields($_txTable);
		$where_clause  = 'pid IN (' . $pidList . ')' . tslib_cObj::enableFields($from_table);
		$groupBy       = '';
		$orderBy       = $sorting;
		$limit         = '';
		//  query (built for Logging):
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CALL \'getFormData()\', SQL: ' . $sql, $this->prefixId, -1);
		}

		//  result:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
		if (!empty ($err)) {
		//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CALL \'getFormData()\', SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
			}
			return $data;
		}
		//  :TODO:localization:
		/*
		$_L = t3lib_div::_GP('L');  --> see EXT:Browser/pi1/class.browser_pi1_localization.php
		tx_advertiser_pi3::log(__LINE__, 'CALL \'getFormData()\', need localisation?', -1, array('sys_language_content' => $GLOBALS['TSFE']->sys_language_content, 'L' => t3lib_div::_GP('L')));
		*/

		## :TODO: localisation using $GLOBALS['TSFE']->sys_page->getRecordOverlay()
		/*
		while($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// get the translated record if the content language is not the default language
			if ($_L > 0) {
				##$OLmode = ($this->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
				$temp = $GLOBALS['TSFE']->sys_page->getRecordOverlay($_txTable, $temp, $_L);
			}
			$data[] = $temp;
		}
		*/
		## tmp solution

		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$_arrKey = 'x' . (string)$ftc['uid'];
			$data[$_arrKey] = array(
				'caption' => htmlspecialchars($ftc['caption']),
				'value'   => $ftc['uid'],
			);
		}
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getFormData()\', default values', $this->prefixId, 0);
		}
		//  localisation  :TODO: need update
		if ($_L > 0 AND empty($_hidel10n)) {
			$where_clause  = 'sys_language_uid = ' . $_L . ' AND pid IN (' . TX_ADVERTISER_PI3_PIDLIST . ')' . tslib_cObj::enableFields($_txTable);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$_lData = array();
			while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$_arrKey = 'x' . (string)$ftc['l10n_parent'];
				$_lData[$_arrKey] = array(
					'caption' => htmlspecialchars($ftc['caption']),
					'value'   => $data[$_arrKey]['value'],
				);
			}
			$data = array_merge($data, $_lData);
		}

		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getFormData()\', return value', $this->prefixId, 0, $data);
		}

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * Method for default value for form data via formidable
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * only needed for edit mode
	 *
	 * @param	string	$case: field to get current value
	 * @param	int		$uid_local: The uid of current edited record
	 * @return	integer uid_foreign: uid of current selected value
	 */
	function getFormValue($case) {
		if (empty($this->recordUid)) {
			return;
		}

		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getFormValue()\' with parameter case: ' . $case, $this->prefixId, 0, $data);
		}

		//  prepare query for items:
		$select_fields = 'uid_foreign';
		$from_table    = 'tx_advertiser_ads_' . $case . '_mm';
		$where_clause  = 'uid_local = ' . $this->recordUid;
		$groupBy       = '';
		$orderBy       = 'sorting ASC';
		$limit         = '';

		//  query (built for Logging):
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CALL \'getFormValue()\', SQL: ' . $sql, $this->prefixId, -1);
		}
		//  result:
		$uid_foreign = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$uid_foreign[] = $ftc['uid_foreign'];
		}
		$uid_foreign = implode(',', $uid_foreign);

		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getFormValue()\', return value: ' . $uid_foreign, $this->prefixId, 0);
		}

		return $uid_foreign;
	}

	// -------------------------------------------------------------------------
	/**
	 * Static method for filling form data via formidable (special case: tax rates)
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * if selects/ radios etc. have to get their options from a related table
	 *
	 * @param	string	$case: field to get possible values
	 * @param	bool	$prependEmpty: wether an empty row has to be prepended
	 * @:ToDo: add desc
	 * @return	array   $data
	 */
	function getTaxData($prependEmpty = false, $pid = null, $_hidel10n = false, $forceFirstIfEmpty = TRUE) {
		$data = $prependEmpty === false ? array() : array('x0' => array('caption' => '---', 'value' => ''));
			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getTaxData()\' with parameter table: ' . $table, $this->prefixId, 0);
		}

		$pidList = is_null($pid) ? TX_ADVERTISER_PI3_PIDLIST : $pid;
			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CALL \'getTaxData()\' with pidList: ' . $pidList, $this->prefixId, -1);
		}

			//  exec_SELECT_mm_query() uses full joins, we need a left join:
		$select_fields = 'tx_advertiser_vat.uid, tx_advertiser_vat.title, tx_advertiser_vat.title_lang_ol';
		$from_table    = 'tx_advertiser_vat LEFT JOIN tx_advertiser_vat_country_mm mm ON tx_advertiser_vat.uid = mm.uid_local LEFT JOIN static_countries ft ON mm.uid_foreign = ft.uid';
		$where_clause  = '(ft.cn_iso_3 IS NULL OR ft.cn_iso_3 = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($GLOBALS['TSFE']->fe_user->user['static_info_country']) . ')';
		$where_clause .= $this->cObj->enableFields('tx_advertiser_vat');
		$groupBy       = '';
		$orderBy       = 'tx_advertiser_vat.sorting';
		$limit         = '';
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('SQL: get tax items: QUERY: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		if (!empty ($err)) {
				//	log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('CALL \'getTaxData()\', SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
			}
			return $data;
		}
		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$_arrKey = 'x' . (string)$ftc['uid'];
		//  :TODO:localization:
			$data[$_arrKey] = array(
				'caption' => htmlspecialchars($ftc['title']),
				'value'   => $ftc['uid'],
			);
		}

			//
		$count = count ($data);
		if ($count == 0 AND $forceFirstIfEmpty === TRUE) {
		    $data = array(
				'x0' => array(
					'caption' => $this->pi_getLL('notapplicable'),
					'value'   => 0,
				)
			);
		}


		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('CALL \'getTaxData()\', default values', $this->prefixId, -1, $data);
		}

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * method for filling form data via formidable (special case: dwelltime)
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * if selects/ radios etc. have to get their options from a related table
	 *
	 * @param	string	$case: field to get possible values
	 * @param	bool	$prependEmpty: wether an empty row has to be prepended
	 * @return	array   $data
	 * @ToDo revise this description; add comments
	 */
	function getDwelltimeData($prependEmpty = false) {
		$data = $prependEmpty === false ? array() : array('x0' => array('caption' => '---', 'value' => ''));
			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			t3lib_div::devlog('CALL \'getDwelltimeData()\'', $this->prefixId, 0);
		}

			//  available dwelltimes to array
		$dwelltimes = t3lib_div::trimExplode(chr(10), $this->adClassProperties['dwelltime']);
			//  assigned prices
		$prices = $this->getPaymentOption('price');
			//  assigned credit_consumptions
		$credit_consumptions = $this->getPaymentOption('credit_consumption');
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$dataLogArray = array(
				'dwelltimes'          => $dwelltimes,
				'prices'              => $prices,
				'credit_consumptions' => $credit_consumptions,
			);
			t3lib_div::devlog('CALL \'getDwelltimeData()\', default values', $this->prefixId, -1, $dataLogArray);
		}


		@session_start();


		if (isset($this->adClassProperties['useTrial']) AND $this->adClassProperties['useTrial'] === TRUE) {
			$data[0] = array(
			    'caption' => sprintf($this->pi_getLL('trialcaption'), $dwelltimes[0]),
			    'value'   => $dwelltimes[0],
			);
			$_SESSION[$this->prefixId]['payment'][$dwelltimes[0]]['type']   = 'trial';
			$_SESSION[$this->prefixId]['payment'][$dwelltimes[0]]['amount'] = '0.00';
		} else {
				//  loop through dwelltimes and build data array
			$this->usePaymentCredit = 0;

			foreach ($dwelltimes as $dKey => $dVal) {
				$caption = $dVal;
				if (!empty ($this->conf['stdWrap.']['dwelltime.']) AND is_array($this->conf['stdWrap.']['dwelltime.'])) {
					$caption .= $this->cObj->stdWrap('', $this->conf['stdWrap.']['dwelltime.']);
				}

					//  payment via credits available?
				if (!is_null($credit_consumptions) AND !empty($credit_consumptions[$dKey]) AND $this->userCredit['points'] >= $credit_consumptions[$dKey]) {
					$this->usePaymentCredit = 1;
					$data[$dKey]['caption'] = $caption;

					if ($credit_consumptions[$dKey] > 1) {
						$confPart = 'plural';
					} else {
						$confPart = 'singular';
					}
					$data[$dKey]['caption'] .= $this->cObj->stdWrap('', $this->conf['stdWrap.']['credit_consumption.'][$confPart . '.']);
					$data[$dKey]['caption']  = sprintf($data[$dKey]['caption'], $credit_consumptions[$dKey]);
					$data[$dKey]['value']    = $dVal;
						//  store data in Session for accounting
					$_SESSION[$this->prefixId]['payment'][$dVal]['type']   = 'creditconsumption';
					$_SESSION[$this->prefixId]['payment'][$dVal]['amount'] = $credit_consumptions[$dKey];
				}
				if ($this->usePaymentCredit == 0 AND !empty ($prices[$dKey])) {
					$data[$dKey]['caption'] = $caption;

					$vatAnnex = '';
					if ($this->adClassProperties['vatincluded'] == 'included') {
						if (!empty ($this->conf['stdWrap.']['vat.']['included.'])) {
							$vatAnnex = $this->cObj->stdWrap('', $this->conf['stdWrap.']['vat.']['included.']);
							$vatAnnex = sprintf($vatAnnex, $this->adClassProperties['vatrate']);
						}
					} elseif ($this->adClassProperties['vatincluded'] == 'excluded') {
						if (!empty ($this->conf['stdWrap.']['vat.']['excluded.'])) {
							$vatAnnex = $this->cObj->stdWrap('', $this->conf['stdWrap.']['vat.']['excluded.']);
							$vatAnnex = sprintf($vatAnnex, $this->adClassProperties['vatrate']);
						}
					}

					$data[$dKey]['caption'] .= $this->cObj->stdWrap($prices[$dKey], $this->conf['stdWrap.']['price.']);
					$data[$dKey]['caption']  = sprintf($data[$dKey]['caption'], $vatAnnex);
					$data[$dKey]['value']    = $dVal;
						//  store data in Session for accounting
					$_SESSION[$this->prefixId]['payment'][$dVal]['type']   = 'price';
					$_SESSION[$this->prefixId]['payment'][$dVal]['amount'] = $prices[$dKey];
				}
			}
		}

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * method for setting default value for dwelltime selection if there is only one value
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * if selects/ radios etc. have to get their options from a related table
	 *
	 * @return	(int) default dwelltime value
	 * @ToDo revise this description; add comments
	 */
	function getDwelltimeDefault() {
		$dwelltimeDefault = NULL;

		$dwelltimesAvailable = t3lib_div::trimExplode(chr(10), $this->adClassProperties['dwelltime']);
		$dwelltimesCount     =  count($dwelltimesAvailable);
		if ($dwelltimesCount == 1 OR !empty ($this->adClassProperties['useTrial'])) {
			$dwelltimeDefault = $dwelltimesAvailable[0];
		}

			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$debugData = array(
				'dwelltimesAvailable' => $dwelltimesAvailable,
				'dwelltimesCount'           => $dwelltimesCount,
				'dwelltimeDefault'          => $dwelltimeDefault,
			);
			t3lib_div::devlog('CALL \'getDwelltimeDefault()\' result', $this->prefixId, -1, $debugData);
		}

		return $dwelltimeDefault;
	}

	// -------------------------------------------------------------------------
	/**
	 * Static method for filling form data via formidable (special case: options)
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * if selects/ radios etc. have to get their options from a related table
	 *
	 * @param	string	$case: field to get possible values
	 * @return	array   $data
	 * @ToDo revise this description; add comments
	 */
	function getOptionData($case = 'premium') {
		$uGroup  =& $this->adClassProperties['fe_group_access'];
		$payment =  $this->usePaymentCredit ? 'creditconsumption' : 'price';

		$lConf   =& $this->conf['options.'][$case . '.'];
		$pConf   =  $lConf[$payment . '.'][$uGroup];  //  price as float

		$caption =  sprintf($lConf['sprintf'], $pConf);  //  money format
		$caption =  sprintf($lConf['caption'], $caption, $lConf['dwelltime']);

		$data['item'] = array(
			'caption' => $caption,
			'value'   => 1,  ##  $pConf,
		);
			//  store data in Session for accounting
		$_SESSION[$this->prefixId]['invoice']['options'][$case] = array(
##			$data['item'];
			'caption' => $data['item']['caption'],
			'value'   => $pConf,
		);

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * ### add desc ###
	 */
	function getPaymentOption($case) {
		$paymentOption = NULL;
		if (trim($case) == '') {
			return $paymentOption;
		}
		$paymentOption = t3lib_div::trimExplode(chr(10), $this->adClassProperties[$case]);

		return $paymentOption;
	}

	// -------------------------------------------------------------------------
	/**
	 * parse diffenrece between today and timestamp from db as number of years/ months past
	 *
	 * used as <userobj> in formidable form config file (xml)
	 * only needed for edit mode
	 *
	 * @param	string	$field: Name of field in local table containing the timestamp
	 * @param	string	$case: y(ears) || m(onths)
	 * @return	integer
	 */
	function parseDate($field, $case) {
		//  only in edit mode
		if (empty ($this->recordUid)) {
			return;
		}

		//  prepare query for items:
		$select_fields = $field;
		$from_table    = 'tx_advertiser_ads';
		$where_clause  = 'uid = ' . $this->recordUid;
		$groupBy       = '';
		$orderBy       = '';
		$limit         = 1;

		//  query (built for Logging):
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: QUERY: ' . $sql, $this->prefixId, -1);
		}
		//  query:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		//	log
		if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
			t3lib_div::devlog('PARSE DATE: SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
			return;
		}
		$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: result: ' . $ftc['dateofproduction'], $this->prefixId, -1);
		}
		//  if result is 0 it means no value
		if (empty($ftc['dateofproduction'])) {
			return;
		} else {
			$val = time() - $ftc['dateofproduction'];
		}

		//  parse it
		$result = '';
		$secondsInAYear = 365 * 24 * 60 * 60;
		//  number of years:
		$years = floor($val / $secondsInAYear);
		switch ($case) {
			case 'y':
				$result = $years;
				break;
			case 'm':
				$secondsInAMonth = 30 * 24 * 60 * 60;
				$months = $val - ($years * $secondsInAYear);
				$result = floor($months / $secondsInAMonth);
				break;
		}

			//	log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: result (' . $case . '): ' . $result, $this->prefixId, -1);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	/**
	 * Method for storing data in mm tables
	 *
	 * @param	string		Table name (without pre- and suffix, e.g. 'price_mode')
	 * @param	integer		uid of local record
	 * @param	mixed		uid (or array with uids) of foreign record(s)
	 * @return	bool		local value (mm relation true/false)
	 */
	function storeMmData($mm_table, $uid_local, $uid_foreign) {
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 1) {
			t3lib_div::devlog('FUNCTION CALL: storeMmData($mm_table = ' . $mm_table . ', $uid_local = ' . $uid_local . ', $uid_foreign = ' . $uid_foreign . ')', 'advertiser', 1);
		}
		// no mm relation?
		if (empty ($uid_foreign) OR (count($uid_foreign) == 0)) {
			return 0;
		}

		// $uid_foreign is always array
		if (!is_array($uid_foreign)) {
			$uid_foreign = array($uid_foreign);
		}
		// full table name
		$table = $this->extKey_tx . '_ads_' . $mm_table . '_mm';

		// delete current relations if set
		$where = 'uid_local = ' . (int)$uid_local;
		$sql = $GLOBALS['TYPO3_DB']->DELETEquery($table, $where);
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('EDIT AD: delete current mm: ' . $sql, $this->prefixId, -1);
		}
		$GLOBALS['TYPO3_DB']->exec_DELETEquery($table, $where);

		// insert new relations
		$i = 0;
		foreach ($uid_foreign as $ufVal) {
			$i++;
			$fields_values = array(
				'uid_local'   => $uid_local,
				'uid_foreign' => (int)$ufVal,
				'tablenames'  => '',
				'sorting'     => $i,
			);
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				$sql = $GLOBALS['TYPO3_DB']->INSERTquery($table, $fields_values, $no_quote_fields = false);
				t3lib_div::devlog('EDIT AD: new mm: ' . $sql, $this->prefixId, -1);
			}
			$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $fields_values, $no_quote_fields = false);
			$err = $GLOBALS['TYPO3_DB']->sql_error();
			//	log
			if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('STORE mm data: SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
			}
		}

		return 1;
	}

	// -------------------------------------------------------------------------
	/**
	 * send control mail to admin
	 *
	 * @return	void
	 */

	function adminMail($ad) {
		$lConf = $this->conf['mail.'];
			// log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('MAIL: conf (Array).', $this->prefixId, -1, $lConf);
		}

			// mail to
		$to   =& $lConf['to'];
		if (empty ($to)) {
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('MAIL: ERROR conf: \'to\' isn\'t set. Please configure typoscript: "plugin.tx_advertiser_pi3.mail.to" and set a valid email recipient address! Abort.', $this->prefixId, 3);
			}
			return;
		}

			// mail from
		$from =& $lConf['from'];
		if (empty ($from) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
			t3lib_div::devlog('MAIL: ERROR conf: \'to\' isn\'t set. Please configure typoscript: "plugin.tx_advertiser_pi3.mail.from" and set a valid email sender address!', $this->prefixId, 3);
		}

			// mail subject
		$subject = $lConf['subject'];

			/* mail body */
			//  replace pairs
		$_adID = '(unknown)';
		$_adID = !empty ($this->recordUid) ? $this->recordUid : $_adID;
		$_adID = !empty ($ad['id'])        ? $ad['id']        : $_adID;
			//  get category label: depends on pid and type
		$_entry  = $this->pi_getRecord($table = 'tx_advertiser_ads', $uid = (int)$_adID, $checkPage = 0);
		$_cLabel = $this->conf['category_label.'][$_entry['pid'] . '.'][$_entry['type']];
		$markerArray  = array(
			'###ADS_ID###'              => $_adID,
			'###ADS_CATEGORY###'        => $_cLabel,
			'###ADVERTISER_NAME###'     => $GLOBALS['TSFE']->fe_user->user['name'],
			'###ADVERTISER_USERNAME###' => $GLOBALS['TSFE']->fe_user->user['username'],
			'###ADS_PID###'             => (int)$this->conf['pidList'],
			'###ADS_TITLE###'           => $ad['title'],
			'###ADS_DESCRIPTION###'     => strip_tags($ad['description']),
			'###ADS_PRICE###'           => $ad['price'],
		);
		$message = strtr($lConf['message'], $markerArray);
			// log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('MAIL message body after replacement: ' . $message, $this->prefixId, -1);
		}

		if ($lConf['metaCharset'] == 'UTF-8' AND $lConf['charset'] != 'UTF-8') {
			$subject = utf8_decode($subject);
			$message = utf8_decode($message);
		}
			// log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('MAIL message body after charset replacement: ' . $message, $this->prefixId, -1);
		}

			// mail headers
		$headers = 'From: ' . $lConf['project'] . ' <' . $lConf['from'] . '>
Subject: ' . $subject . '
MIME-Version: 1.0
Content-Type: text/html; charset=' . $lConf['charset'] . '; format=flowed
Content-Transfer-Encoding: quoted-printable
';

			// send mail
		$success = @mail($to, $subject, wordwrap($message, 70), $headers);
			// log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
			$msg = array(
				'to'       => $to,
				'subject'  => $subject,
				'message'  => $message,
				'headers'  => $headers,
				'success'  => (int)$success,
			);
			if ($msg['success'] == 0) {
				$msg['errormsg'] = error_get_last();
			}
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
				t3lib_div::devlog('MAIL should be sendt.', $this->prefixId, 0, $msg);
			}
		}
	}

	// -------------------------------------------------------------------------
	/**
	 * Redirect to image edit after ad edit
	 *
	 * @return	void
	 * @obsolete
	 */
	function redirect2imageForm() {
		$id            = $this->conf['editImagesPid'];
		$urlParameters = array(
			$this->prefixId . '[recordUid]' => $_SESSION['tx_advertiser_pi3']['recordUid'],
		);
		$baseURL       = $GLOBALS['TSFE']->config['config']['baseURL'];
		//  check for trailing slash
		if (!preg_match('%\/$%', $baseURL)) {
			$baseURL .= '/';
		}
		$pageLink      = $baseURL . $this->pi_getPageLink($id, $target='', $urlParameters);

		header('Location: ' . $pageLink);
		exit;
	}

	// -------------------------------------------------------------------------
	/**
	 * :TODO: desc
	 *
	 * @return	void
	 * @obsolete
	 */
	function redirect2preview() {
		$id            = $this->conf['previewPid'];
		$urlParameters = array(
			'tx_browser_pi1[showUid]' => $_SESSION['tx_advertiser_pi3']['recordUid'],
		);
		$pageLink      = $GLOBALS['TSFE']->config['config']['baseURL'];
			//  check for trailing slash
		if (!preg_match('%\/$%', $pageLink)) {
			$pageLink .= '/';
		}
		$pageLink     .= $this->pi_getPageLink($id, $target='', $urlParameters);
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 2) {
			t3lib_div::devlog('NEW AD: redirect to preview. New target is: ' . $pageLink, $this->prefixId, 2);
		}

		header('Location: ' . $pageLink);
		exit;
	}

	// -------------------------------------------------------------------------
	/**
	 * :TODO: desc
	 *
	 * @return	void
	 * @obsolete
	 */
	function sessionCacheInsert() {
		$sessioncontent = array(
			'adClassProperties' => $_SESSION['tx_advertiser_pi3']['adClassProperties'],
			'invoice'           => $_SESSION['tx_advertiser_pi3']['invoice'],
			'optionsselected'   => $_SESSION['tx_advertiser_pi3']['optionsselected'],
			'payment'           => $_SESSION['tx_advertiser_pi3']['payment'],
		);
		$sessioncontent = serialize($sessioncontent);
		$table = $this->extKey_tx . '_sessioncache';
		$fields_values = array(
			'tstamp'         => time(),
			'crdate'         => time(),
			'ad'             => $_SESSION['tx_advertiser_pi3']['recordUid'],
			'sessioncontent' => $sessioncontent,
		);
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->INSERTquery($table, $fields_values);
			t3lib_div::devlog('SESSION CACHE inserted: ' . $sql, $this->prefixId, -1);
		}
		$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $fields_values, $no_quote_fields = FALSE);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		//	log
		if (!empty ($err) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
			t3lib_div::devlog('SESSION CACHE inserted: SQL error: QUERY: ' . $sql . ': ERROR: ' . $err, $this->prefixId, 3);
		}
	}

	// -------------------------------------------------------------------------
	/**
	 * TEMP: Zone of ad owner
	 *
	 * @return	string
	 * obsolete
	 */
	function tmpGetOwnerZone() {
		$zn_code  = $GLOBALS['TSFE']->fe_user->user['zone'];
		$res      = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields='zn_name_local',$from_table='static_country_zones',$where_clause='zn_code = "' . $zn_code . '"',$groupBy='',$orderBy='',$limit='1');
		$ftc      = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		return $ftc['zn_name_local'];
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi3/class.tx_advertiser_pi3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi3/class.tx_advertiser_pi3.php']);
}
?>