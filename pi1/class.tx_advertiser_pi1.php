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
 *
 *
 *   64: class tx_advertiser_pi1 extends tslib_pibase
 *   79:     public function main($content, $conf)
 *  132:     public function getData()
 *  265:     public function groupData($data)
 *  377:     public function getTemplate(&$data)
 *  424:     public function getMarkerArray($data)
 *  444:     public function getText($data)
 *  460:     public function getImages($data, &$markerArray)
 *  509:     public function getFiles($data)
 *  534:     public function getDataList($data, $mmData = array())
 *  597:     public function getMMDatalist($mmData)
 *  608:     public function getOwner()
 *  684:     public function getVatInfo()
 *  706:     public function getMerchantlogo($image)
 *  744:     public function getMailForm()
 *  946:     public function getPreviewForm()
 * 1232:     public function amountSplit($amount)
 * 1276:     public function parseDate($value, $formatY, $formatM)
 *
 * TOTAL FUNCTIONS: 17
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Advertiser: Single View' for the 'advertiser' extension.
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_advertiser
 */
class tx_advertiser_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_advertiser_pi1';                // Same as class name
	var $scriptRelPath = 'pi1/class.tx_advertiser_pi1.php';  // Path to this script relative to the extension dir.
	var $extKey        = 'advertiser';                       // The extension key.
	var $extKey_tx     = 'tx_advertiser';                    // The prefixed extension key.
	var $blacklist     = array(
		'patriclinton@yahoo.com',
	);

	// -------------------------------------------------------------------------
	/**
	 * The main method of the PlugIn
	 *
	 * @param   string      $content: The content of the PlugIn
	 * @param   array       $conf: The PlugIn Configuration
	 * @return  The content that should be displayed on the website
	 * @access public
	 */
	public function main($content, $conf) {
			//  prepare plugin config
		$this->conf =& $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$conf_ext   = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->conf = array_merge($this->conf, $conf_ext);
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('CONF', $this->prefixId, -1, $this->conf);
		}

			//	current record: get from piVars of plugin: tx_browser_pi1
		$currentRecord = t3lib_div::_GP('tx_browser_pi1');
		if (empty ($currentRecord['showUid']) AND !empty ($_SESSION['tx_advertiser_pi3']['recordUid'])) {
			$currentRecord['showUid'] = $_SESSION['tx_advertiser_pi3']['recordUid'];
		}
		if (!empty ($_SESSION['tx_advertiser_pi3']['recordUid']) AND $currentRecord['showUid'] == $_SESSION['tx_advertiser_pi3']['recordUid']) {
			$this->isPreview = TRUE;
		} else {
			$this->isPreview = FALSE;
		}

			//  check input validity
		if (empty ($currentRecord['showUid'])) {
				// error: no current record
			$errorContent  = $this->cObj->dataWrap($this->pi_getLL('errorNoShowUidHeader'), $this->conf['error_general_dataWrap.']['header']);
			$errorContent .= $this->cObj->dataWrap($this->pi_getLL('errorNoShowUidText'),   $this->conf['error_general_dataWrap.']['text']);
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 2) {
				t3lib_div::devlog('ERROR: current record is empty!', $this->prefixId, 2);
			}
		}
		if (!is_numeric($currentRecord['showUid'])) {
				// error: current record isn't a number
			$errorContent  = $this->cObj->dataWrap($this->pi_getLL('errorWrongShowUidHeader'), $this->conf['error_general_dataWrap.']['header']);
			$errorContent .= $this->cObj->dataWrap($this->pi_getLL('errorWrongShowUidText'),   $this->conf['error_general_dataWrap.']['text']);
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 2) {
				t3lib_div::devlog('ERROR: current record isn\'t a number! [' . $currentRecord['showUid'] . ']', $this->prefixId, 2);
			}
		}
		if (!empty ($errorContent)) {
			$errorContent = $this->cObj->dataWrap($errorContent, $this->conf['error_general_dataWrap']);
			return $errorContent;
		}

			//  keep it for mail form
		$this->currentRecord = (int)$currentRecord['showUid'];
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('Current record', $this->prefixId, -1, $currentRecord);
		}
			//	add current record to piVars
		$this->piVars = array_merge($this->piVars, $currentRecord);

		$data    = $this->getData();
		$data    = $this->groupData($data);
		$content = $this->getTemplate($data);

##		return $this->pi_wrapInBaseClass($content);
		return '<div class="tx-arj-1">' . $content . '</div>';
	}

	// -------------------------------------------------------------------------
	/**
	 * Gets a single item from the database
	 *
	 * @param   string   $content: The PlugIn content
	 * @return  array of single record data
	 * @access public
	 */
	public function getData() {
		$lConf =& $this->conf;

			//  get configurated fields and tables
		$fields = explode(',', $lConf['select']);
		$tables = array();
		foreach ($fields as $fVal) {
			@list($t) = explode('.', $fVal);
			$tables[] = trim($t);
		}
		$localTable = array_shift($tables);
		$tables = array_unique($tables);
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('SQL: tables + fields', $this->prefixId, -1, array($tables, $fields));
		}
		$joinTables = array();
		if (is_array($lConf['relations.']['simple.'])) {
			foreach ($lConf['relations.']['simple.'] as $jtKey => $jtVal) {
				$joinTables[$jtKey] = $jtVal;
			}
		}
		$mmTables = array();
		if (is_array($lConf['relations.']['mm.'])) {
			foreach ($lConf['relations.']['mm.'] as $mtKey => $mtVal) {
				$mmTables[trim($mtKey)] = trim($mtVal);
			}
		}

			//  build query
		$sql = 'SELECT ' . $localTable . '.uid as "' . $localTable . '.uid"';
		$mmFields = array();
		foreach ($fields as $fVal) {
			$fVal = trim($fVal);
				//  skip mm-related fields
			@list($_t) = explode('.', $fVal);
			if (array_key_exists(trim($_t), $mmTables)) {
				$mmFields[] = $fVal;
				continue;
			}
			if ($fVal != $localTable . '.uid') {
				$sql .= ', ' . $fVal . ' AS "' . $fVal . '"';
			}
		}
		$sql .= "\n" . 'FROM ' . $localTable;
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
			t3lib_div::devlog('SQL: QUERY: ' . $sql, $this->prefixId, 1);
		}
			//  add joined tables
		foreach ($joinTables as $jtKey => $jtVal) {
			$_foreignTable = $jtKey;
				// :TODO: check jtVal is filled
			foreach ($jtVal as $jtvKey => $jtvVal) {
				$_foreignTable .= $jtvKey;
				$_localTable    = $jtvVal;
			}
			@list($_foreignTable, $_foreignTableKeyField) = explode('.', $_foreignTable);
			$sql .= "\n" . 'LEFT JOIN ' . $_foreignTable . ' ON ' . $_foreignTable . '.' . $_foreignTableKeyField . ' = ' . $_localTable;
		}
		$sql .= "\n" . 'WHERE ' . $localTable . '.uid = ' . (int)$this->piVars['showUid'];
		if ($this->isPreview === FALSE) {
				//  �ffentlicher Modus: ber�cksichtige alle Ausschlussgr�nde
			$sql .= ' AND ' . $localTable . '.ispreview = 0' . $this->cObj->enableFields($localTable, $show_hidden = 0);
		} else {
				//  Beschr�nkung enableFields auf hidden/ deleted
			$sql .= ' AND tx_advertiser_ads.deleted=0 AND tx_advertiser_ads.hidden=0';
		}
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
			t3lib_div::devlog('SQL: QUERY with joins: ' . $sql, $this->prefixId, 1);
		}

		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		if (!$res) {
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('SQL: ERROR: ' . $GLOBALS['TYPO3_DB']->sql_error(), $this->prefixId, 3);
			}
		} else {
			$numRows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($numRows < 1) {
				header('HTTP/1.0 404 Not Found');

				$_desc = sprintf($this->pi_getLL('ad_notfound_desc'), '<p>', '</p>', '<ul>', '</ul>', '<li>', '</li>');
				$data = array(
					'sgData' => array(
						'tx_advertiser_ads.uid' => 0,
						'tx_advertiser_manufacturers.title' => $this->pi_getLL('ad_notfound_title'),
						'tx_advertiser_ads.title' => $this->pi_getLL('ad_notfound_header'),
						'tx_advertiser_ads.description' => $_desc,
					),
					'mmData' => NULL,
				);
				$this->notFound = TRUE;

					//  SEO: change page title (and store title for using later)
				$GLOBALS['TSFE']->page['title']                = $data['sgData']['tx_advertiser_manufacturers.title'] . ' ' . $data['sgData']['tx_advertiser_ads.title'];
				$this->hiddenFields['tx_advertiser_ads.title'] = $data['sgData']['tx_advertiser_ads.title'];

			    return $data;
			}

			$sgData  = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
				t3lib_div::devlog('SQL: RESULT', $this->prefixId, 1, $sgData);
			}

				//  SEO: change page title (and store title for using later)
			$GLOBALS['TSFE']->page['title']                = $sgData['tx_advertiser_manufacturers.title'] . ' ' . $sgData['tx_advertiser_ads.title'];
			$this->hiddenFields['tx_advertiser_ads.title'] = $sgData['tx_advertiser_ads.title'];
		}

			//  get mm-related data
		$mmData = array();
		foreach ($mmFields as $mtVal) {
			@list($_t, $_f) = explode('.', $mtVal);
			$_mmTable      = 'tx_advertiser_ads_' . $mmTables[$_t] . '_mm';
			$sql = 'SELECT ' . $mtVal . '
					FROM ' . $_mmTable . '
					LEFT JOIN ' . $_t . '
					  ON ' . $_mmTable . '.uid_foreign = ' . $_t . '.uid
					WHERE `uid_local` = ' . (int)$this->piVars['showUid'];
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
				t3lib_div::devlog('SQL: QUERY with MM relation: ' . $sql, $this->prefixId, 1);
			}

			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			if (!$res) {
					//  log
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
					t3lib_div::devlog('SQL: ERROR: ' . $GLOBALS['TYPO3_DB']->sql_error(), $this->prefixId, 3);
				}
			} else {
				while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$mmData[$mtVal][] = $ftc[$_f];
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
		}
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
			t3lib_div::devlog('SQL: RESULT', $this->prefixId, 1, $mmData);
		}

		$data = array(
			'sgData' => $sgData,
			'mmData' => $mmData,
		);

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * group data accordingly to conf[handleAs]
	 *
	 * @param   array   $data: record data
	 * @return  array of grouped data
	 * @access public
	 */
	public function groupData($data) {
		$lConf =& $this->conf['handleAs.'];
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('groupData: DATA', $this->prefixId, 1, $data);
			t3lib_div::devlog('groupData: field handlers', $this->prefixId, -1, $lConf);
		}

			//  get fields handled as marker
		$fieldsMarker = array();
		$confMarker   = explode(',', $lConf['marker']);
		foreach ($confMarker as $fmVal) {
			$fmVal = trim($fmVal);
			$sConf = strtr($fmVal, '.', '_') . '.';

				// convert tx_advertiser_ads.productlink:
			if ($fmVal == 'tx_advertiser_ads.productlink') {
				if (!empty ($data['sgData'][$fmVal])) {
						//  prefix with leading 'http:' if necessary
					if (!preg_match('%^http:%', $data['sgData'][$fmVal])) {
						$data['sgData'][$fmVal] = 'http://'. $data['sgData'][$fmVal];
					}
					$this->productLink = $data['sgData'][$fmVal];
					$_tpConf = array(
						'parameter' => $this->productLink,
					);
					$data['sgData'][$fmVal] = $this->cObj->typoLink($this->pi_getLL('buyNow'), $_tpConf) ;
				}
			}

				//  skip empty values?
			if (empty ($this->conf['clean_up.']['ifEmpty.']['marker']) OR (!empty($data['sgData'][$fmVal]) AND $data['sgData'][$fmVal] != '0.00')) {
				$fieldsMarker[$fmVal] = $this->cObj->stdWrap($data['sgData'][$fmVal], $this->conf[$sConf]);
			} else {
				$fieldsMarker[$fmVal] = '';
			}
			unset($data['sgData'][$fmVal]);
		}

			//  get fields handled as text
		$fieldsText = array();
		$confText   = explode(',', $lConf['text']);
		foreach ($confText as $ftVal) {
			$ftVal = trim($ftVal);
			$fieldsText[$ftVal] = $data['sgData'][$ftVal];
			unset($data['sgData'][$ftVal]);
		}

			//  get fields handled as image
		$fieldsImage = array();
		$confImage   = explode(',', $lConf['image']);
		foreach ($confImage as $fiVal) {
			$fiVal = trim($fiVal);
			$fieldsImage[$fiVal] = $data['sgData'][$fiVal];
			unset($data['sgData'][$fiVal]);
		}

			//  get fields handled as file
		$fieldsFile = array();
		$confFile   = explode(',', $lConf['file']);
		foreach ($confFile as $ffVal) {
			$ffVal = trim($ffVal);
			$fieldsFile[$ffVal] = $data['sgData'][$ffVal];
			unset($data['sgData'][$ffVal]);
		}

			//  clean_up list fields?
		if ($this->conf['clean_up.']['ifEmpty.']['list']) {
			$clean_upFields = explode(',', $this->conf['clean_up.']['fields']);
			foreach ($clean_upFields as $cfKey => $cfVal) {
				$clean_upFields[$cfKey] = trim($cfVal);
			}
			foreach ($data['sgData'] as $sgKey => $sgVal) {
				if (empty ($sgVal) OR in_array($sgKey, $clean_upFields) OR $sgVal == '0.00') {
						//  save date for using later
					if (in_array($sgKey, $clean_upFields)) {
						$this->hiddenFields[$sgKey] = $sgVal;
					}
					unset($data['sgData'][$sgKey]);
				}
			}
		}

			//  get fields with mm-related values
		$fieldsMM = array();
		foreach ($data['mmData'] as $mmKey => $mmVal) {
			$data['sgData'][$mmKey] = $this->getMMDatalist($mmVal);
		}

		$data = array(
			'fieldsMarker' => $fieldsMarker,
			'fieldsText'   => $fieldsText,
			'fieldsImage'  => $fieldsImage,
			'fieldsFile'   => $fieldsFile,
			'sgData'       => $data['sgData'],
			'mmData'       => $data['mmData'],
		);
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('groupData: grouped Data', $this->prefixId, -1, $data);
		}

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * substitutes values in Template
	 *
	 * @param   array   $data: record data
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getTemplate(&$data) {
		$content = '';
		$this->history = -1;
			//  get template file
		$this->templateCode = $this->cObj->fileResource($this->conf['template.']['file']);
		if (empty ($this->templateCode)) {
				//	log + abort
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('TEMPLATE ERROR: no template found.', $this->prefixId, 3);
			}
			return;
		}
		$markerArray = $this->getMarkerArray($data['fieldsMarker']);
		$markerArray['###TEXT###']       = $this->getText($data['fieldsText']);
		$markerArray['###CURRENTURL###'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');  //  for translation via babelfish
		$markerArray['###IMAGE###']      = $this->getImages($data['fieldsImage'], $markerArray);
		$markerArray['###FILE###']       = $this->getFiles($data['fieldsFile']);
		$markerArray['###DATALIST###']   = $this->getDataList($data['sgData'], $data['mmData']);
		$markerArray['###OWNER###']      = $this->getOwner();
		$markerArray['###VATINFO###']    = $this->getVatInfo();
		if ($this->isPreview === TRUE) {
			$markerArray['###MAILFORM###']    = $this->getPreviewForm();
			$markerArray['###BUTTON###']      = $this->pi_getLL('backInEdit');
			$markerArray['###PREVIEWNOTE###'] = $this->cObj->stdWrap($this->pi_getLL('previewNote'), $this->conf['previewNote.']);
		} elseif ($this->notFound === TRUE) {
			$markerArray['###MAILFORM###']    = '';
			$markerArray['###BUTTON###']      = '';
			$markerArray['###PREVIEWNOTE###'] = '';
			$markerArray['###OWNER###']       = '';
		} else {
			$markerArray['###MAILFORM###']    = $this->getMailForm();
			$markerArray['###BUTTON###']      = $this->pi_getLL('back');
			$markerArray['###PREVIEWNOTE###'] = '';
		}
		$markerArray['###HISTORYSTEPS###']  = $this->history;
		$markerArray['###DIRECTCONTACT###'] = $this->pi_getLL('directContact_title');

			// Get template
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_IMGTXT###');
		$content .= $this->cObj->substituteMarkerArray($template, $markerArray);

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * get plain substitued markers
	 *
	 * @param   array   $data: record table data
	 * @return  array of a marker names => values
	 * @access public
	 */
	public function getMarkerArray($data) {
		$markerArray = array();
		foreach ($data as $dKey => $dVal) {
			$dKey = strtr($dKey, '.', '_');
			$dKey = strtoupper($dKey);
			$dKey = '###' . $dKey . '###';
			$markerArray[$dKey] = $dVal;
		}

		return $markerArray;
	}

	// -------------------------------------------------------------------------
	/**
	 * substitutes values in Template and allocate lang labels
	 *
	 * @param   array   $data: record table data
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getText($data) {
		$data = implode('<br />', $data);
		$data = $this->cObj->stdWrap($data, $this->conf['general_stdWrap.']);

		return $data;
	}

	// -------------------------------------------------------------------------
	/**
	 * substitutes values in Template and allocate lang labels
	 *
	 * @param   array   $data: record table data
	 * @param   array   $markerArray: reference to marker array (will be altered)
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getImages($data, &$markerArray) {
		$content = '';

			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 0) {
			t3lib_div::devlog('GET IMAGES: data array.', $this->extKey, 0, $data);
		}
		$i = 0;
		foreach ($data as $dVal) {
			$images = explode(',', $dVal);
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 0) {
				t3lib_div::devlog('GET IMAGES: images array.', $this->extKey, 0, $images);
			}
				//	check if key 0 has content at least
			if (!empty ($images[0])) {
				foreach ($images as $iVal) {
					$i++;
					if ($i == 1) {
						$lConf = $this->conf['imageFirst.'];
					} else {
						$lConf = $this->conf['image.'];
					}
					$lConf['altText']   = $GLOBALS['TSFE']->page['title'];
					$lConf['titleText'] = $GLOBALS['TSFE']->page['title'];
					$lConf['file']      = trim($this->conf['upload']) . $iVal;
					$lConf['wrap']      = str_replace('###COUNTER###', $i, $lConf['wrap']);
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
						t3lib_div::devlog('IMAGE: Ads image(s) (conf)', $this->prefixId, -1, $lConf);
					}

					$content .= $this->cObj->IMAGE($lConf);
				}
			}
		}

		$markerArray['###IMAGECOUNT###'] = $i;

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * substitutes values in Template and allocate lang labels
	 *
	 * @param   array   $data: record table data
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getFiles($data) {
		if (empty ($data) OR count($data) < 1) {
			return '';
		}
		$content = '';

		$lConf = $this->conf['file.'];
		$lConf['path'] = $this->conf['upload'];

		foreach ($data as $dVal) {
			$content .= $this->cObj->filelink($dVal, $lConf);
		}

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * substitutes values in Template and allocate lang labels
	 *
	 * @param   array   $data: record table data
	 * @return  HTML of a single database entry
	 * @access public
	 * @ToDo: add comments
	 */
	public function getDataList($data, $mmData = array()) {
		$content = '';

			// Get template
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_DATALIST###');

			// Get subpart template for data list
		$subTemplate = $this->cObj->getSubpart($template, '###SINGLEBODYROW###');
			// Loop to create repeating content
		$subPartContent = '';
		$i = 0;
		foreach ($data as $dKey => $dVal) {
			if ($dKey == 'tx_advertiser_ads.dateofproduction') {
				if ($this->conf['dateOfProduction.']['asAge'] == 1) {
						//	parse date of production as age (years/ months)
					$dVal = $this->parseDate($dVal, $this->pi_getLL('dateOfProductionAsAge_years'), $this->pi_getLL('dateOfProductionAsAge_months'));
					if ($dVal == '') {
						continue;
					}
						//	/parse date of production
				} else {
						//	strftime date of production
					$format = $this->conf['dateOfProduction.']['strftime.']['default'];
					if (!empty ($GLOBALS['TSFE']->config['config']['language'])) {
						$language = $GLOBALS['TSFE']->config['config']['language'];
						if (!empty ($this->conf['dateOfProduction.']['strftime.'][$language])) {
							$format = $this->conf['dateOfProduction.']['strftime.'][$language];
						}
					}
					$dVal = strftime($format, $dVal);
						//	/strftime date of production
				}
			}

			$i++;
			$sConf = strtr($dKey, '.', '_') . '.';
			$markerArray = array(
				'###VALUE###' =>$this->cObj->stdWrap($dVal, $this->conf[$sConf]),
				'###CLASS###' => $i % 2 == 0 ? 'even' : 'odd',
			);
			$tsLocalLangKey = str_replace('.', '_', $dKey);
			$tsLocalLangVal = $this->pi_getLL($tsLocalLangKey);
			if (!empty ($tsLocalLangVal)) {
				$markerArray['###FIELD###'] = $tsLocalLangVal;
			} else {
				$markerArray['###FIELD###'] = $this->pi_getLL($dKey);
			}
			$subPartContent .= $this->cObj->substituteMarkerArray($subTemplate, $markerArray);
		}

			// Substitute subpart
		return $this->cObj->substituteSubpart($template, '###SINGLEBODYROW###', $subPartContent);
	}

	// -------------------------------------------------------------------------
	/**
	 * array to string (output) for a set of data
	 *
	 * @param   array   $mmData: record table data
	 * @return  string
	 * @access  public
	 * @todo    configuration options for wrapping
	 */
	public function getMMDatalist($mmData) {
		return implode('<br />', $mmData);
	}

	// -------------------------------------------------------------------------
	/**
	 * get owner information
	 *
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getOwner() {
			//  get owners data
		$sql = 'SELECT address, telephone, email, zip, city, image, company, first_name, last_name, tx_advertiser_merchant, tx_advertiser_vat_id, cn_short_local, zn_name_local
				FROM fe_users
				LEFT JOIN static_countries
				  ON static_info_country = static_countries.cn_iso_3
				LEFT JOIN static_country_zones
				  ON fe_users.zone = zn_code
				WHERE fe_users.uid = ' . (int)$this->hiddenFields['tx_advertiser_ads.fe_user'];
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('SQL: QUERY: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		if (!$res) {
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('SQL: ERROR:: ' . $GLOBALS['TYPO3_DB']->sql_error(), $this->prefixId, 3);
			}
		} else {
			$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('SQL: RESULT', $this->prefixId, -1, $ftc);
			}
		}

			//  store data for using later
		$this->hiddenFields['fe_users.email'] = $ftc['email'];
//  ownerType: obsolete
##		$this->ownerType = $ftc['tx_advertiser_merchant'];

		$content = '';

		$markerArray['###MERCHANTLABEL###'] = $this->pi_getLL('merchantlabel');
		$markerArray['###MERCHANTLOGO###']  = $this->getMerchantlogo($ftc['image']);
		$markerArray['###FIRSTNAME###']     = $ftc['first_name'];
		$markerArray['###LASTNAME###']      = $ftc['last_name'];
		$markerArray['###CITY###']          = $ftc['city'];
		$markerArray['###PHONELABEL###']    = $this->pi_getLL('phonelabel');
		$markerArray['###PHONE###']         = $ftc['telephone'];

		switch ($ftc['tx_advertiser_merchant']) {
			case 0:
					// private advertiser
				$markerArray['###PRIVATE###']       = $this->pi_getLL('fe_users.tx_advertiser_private');

					// Get template
				$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_PRIVATEOWNER###');
				$content .= $this->cObj->substituteMarkerArray($template, $markerArray);
				break;
			case 1:
					// commercial advertiser
				$markerArray['###MERCHANT###']      = $this->pi_getLL('fe_users.tx_advertiser_merchant');
				$markerArray['###COMPANY###']       = $ftc['company'];
				if (!empty ($ftc['tx_advertiser_vat_id'])) {
					$markerArray['###VATIDLABEL###']= $this->pi_getLL('fe_users.tx_advertiser_vat_id');
					$markerArray['###VATID###']     = $ftc['tx_advertiser_vat_id'];
				} else {
					$markerArray['###VATIDLABEL###']= '';
					$markerArray['###VATID###']     = '';
				}

					// Get template
				$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_COMMERCIALOWNER###');
				$content .= $this->cObj->substituteMarkerArray($template, $markerArray);
				break;
		}

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * get owner vat information
	 *
	 * @return  HTML of a single database entry
	 * @access public
	 */
	public function getVatInfo() {
		$content = '';
//  ownerType: obsolete
##		if ($this->ownerType == 1) {
			if (!empty ($this->hiddenFields['tx_advertiser_vat.title'])) {
				$content .= $this->hiddenFields['tx_advertiser_vat.title'];
##			} else {
##		        $content .= sprintf($this->pi_getLL('vatDefault'), $this->conf['stdTaxRate']);
			}
			$content = $this->cObj->stdWrap($content, $this->conf['vatinfo.']);
##		}

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * renders fe_users related image(s)
	 *
	 * @return  HTML of image output
	 * @access public
	 */
	public function getMerchantlogo($image) {
		$content = '';

		$images = explode(',', $image);
		$lConf  = $this->conf['imageLogo.'];
		for ($i = 0; $i < $this->conf['imageLogo.']['maxItems']; $i++) {
			$lConf['file'] = trim($this->conf['imageLogo.']['file']) . $image;
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('IMAGE: Merchant logo (conf)', $this->prefixId, -1, $lConf);
			}

			if (empty ($this->productLink)) {
				$content .= $this->cObj->IMAGE($lConf);
			} else {
				$_tpConf = array(
					'parameter' => $this->productLink,
				);
				$content .= $this->cObj->typoLink($this->cObj->IMAGE($lConf), $_tpConf) ;

			}
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('IMAGE: TSFE->lastImageInfo', $this->prefixId, -1, $GLOBALS['TSFE']->lastImageInfo);
			}
		}

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * display all ads of this owner (if more than one)
	 * display mailform and handle inserted data
	 *
	 * @return  HTML whole form
	 * @access public
	 */
	public function getMailForm() {
		$content = '';

			//	other ads of this owner
		$linkToOtherAds = '';
		if ($this->conf['displayOtherAds']) {
				//	count other ads of this owner and show link to all if > 1
			$select_fields = 'COUNT(*) AS num_ads';
			$from_table    = $this->extKey_tx . '_ads';
			$where_clause  = 'fe_user = ' . (int)$this->hiddenFields['tx_advertiser_ads.fe_user'];
			$groupBy       = '';
			$orderBy       = '';
			$limit         = '';
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
				t3lib_div::devlog('Link to other ads: SQL query: ' . $sql, $this->prefixId, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$err = $GLOBALS['TYPO3_DB']->sql_error($res);
			if (!empty ($err)) {
					//	log
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
					t3lib_div::devlog('Link to other ads: SQL error: ' . $err, $this->prefixId, 3);
				}
			} else {
				$urlParameters = array(
					'tx_browser_pi1[advertiser]' => (int)$this->hiddenFields['tx_advertiser_ads.fe_user'],
				);
				$linkToOtherAds = $this->pi_linkToPage($this->pi_getLL('otherAds'), $this->conf['listPid'], $target = '', $urlParameters);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
			//	/other ads of this owner

		$urlParameters = array(
			'no_cache' => 1,
			'tx_browser_pi1[showUid]' => $this->currentRecord,
		);
		$markerArray = array(
			'###OTHERADS###'    => $linkToOtherAds,

			'###FORMACTION###'  => $this->pi_getPageLink($id = $GLOBALS['TSFE']->id, $target = '', $urlParameters),
			'###PIVARPREFIX###' => $this->prefixId,
			'###MAILTOPIC###'   => $this->hiddenFields['tx_advertiser_ads.title'] . ' (InseratsID ' . $this->hiddenFields['tx_advertiser_ads.uid'] . ')',
			'###MAILERROR###'   => '',

			'###MESSAGE_TO_ADVERTISER###' => $this->pi_getLL('message_to_advertiser'),
			'###MESSAGE_YOUR_NAME###'     => $this->pi_getLL('message_your_name'),
			'###MESSAGE_YOUR_EMAIL###'    => $this->pi_getLL('message_your_email'),
			'###MESSAGE_YOUR_PHONE###'    => $this->pi_getLL('message_your_phone'),
			'###MESSAGE_QUESTION###'      => $this->pi_getLL('message_question'),
			'###MESSAGE_YOUR_QUESTION###' => $this->pi_getLL('message_your_question'),
			'###MESSAGE_SEND###'          => $this->pi_getLL('message_send'),
		);
		if (!empty ($this->piVars['mailsendt'])) {
			$markerArray['###MESSAGE_YOUR_NAME###']     = htmlspecialchars($this->piVars['sendername']);
			$markerArray['###MESSAGE_YOUR_EMAIL###']    = htmlspecialchars($this->piVars['sendermail']);
			$markerArray['###MESSAGE_QUESTION###']      = htmlspecialchars($this->piVars['sendertopic']);
			$markerArray['###MESSAGE_YOUR_QUESTION###'] = htmlspecialchars($this->piVars['mailmsg']);
		}

			//  CAPTCHA
		if ($this->conf['captcha'] == 0) {
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
				t3lib_div::devlog('CAPTCHA: no captcha extension selected. --> Change this? See option "Use capcha Extension [captcha]" in EM.', $this->prefixId, 1);
			}
		}
			//  sr_freecap: Invoking the CAPTCHA methods
		if ($this->conf['captcha'] == 1) {
				//	log
			if (is_object($this->freeCap) AND $this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('CAPTCHA: freecap CAPTCHA selected', $this->prefixId, -1);
			}
				//  sr_freecap: Invoking the CAPTCHA methods
			$_freecapIsLoaded = t3lib_extMgm::isLoaded('sr_freecap');
			if ($_freecapIsLoaded) {
				require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
				$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
				if (is_object($this->freeCap)) {
						//	log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
						t3lib_div::devlog('CAPTCHA: freecap CAPTCHA invoked', $this->prefixId, -1);
					}
					$markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
				} else {
						//	log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
						t3lib_div::devlog('CAPTCHA: ERROR: Invoking freecap CAPTCHA failed', $this->prefixId, 3);
					}
					$subpartArray['###CAPTCHA_INSERT###'] = '';
				}
			} else {
					//	log
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
					t3lib_div::devlog('CAPTCHA: ERROR: EXT sr_freecap not loaded.', $this->prefixId, 3);
				}
			}
		}

			// Get template
		$template = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE_MAILFORM###');
		$content .= $this->cObj->substituteMarkerArray($template, $markerArray);

		if (!empty ($this->piVars['mailsendt'])) {
			$_errMsg = '';
			$this->history = $this->piVars['history'];
			$this->history--;

				//	Checking syntax of input email address
			if (!t3lib_div::validEmail($this->piVars['sendermail']) === TRUE) {
				$_mailsendtError = TRUE;
				$_errMsg .= $this->cObj->dataWrap($this->pi_getLL('errorWrongMail'), $this->conf['error_wrongMail_dataWrap']);
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 2) {
					t3lib_div::devlog('MAIL: REQUEST for sending mail, but "' . $this->piVars['sendermail'] . '" is not a valid email address.', $this->prefixId, 2);
				}
			}
				//	Checking the input string against the CAPTCHA string
			elseif (is_object($this->freeCap) && !$this->freeCap->checkWord($this->piVars['captcha_response'])) {
				$_mailsendtError = TRUE;
				$_errMsg .= $this->cObj->dataWrap($this->pi_getLL('errorWrongCaptcha'), $this->conf['error_wrongCaptcha_dataWrap']);
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 2) {
					t3lib_div::devlog('MAIL: REQUEST for sending mail, but "' . $this->piVars['sendermail'] . '" is not a valid email address.', $this->prefixId, 2);
				}
			}
			elseif (in_array($this->piVars['sendermail'], $this->blacklist)) {
				$_mailsendtError = TRUE;
				$_showFormAgain  = FALSE;
				$_errMsg .= $this->cObj->dataWrap($this->pi_getLL('errorSenderBlacklisted'), $this->conf['error_SenderBlacklisted_dataWrap']);
				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 2) {
					t3lib_div::devlog('MAIL: REQUEST for sending mail, but "' . $this->piVars['sendermail'] . '" is blacklisted.', $this->prefixId, 2);
				}
			}
			if (!empty ($_mailsendtError) AND $_showFormAgain !== FALSE) {
				$content = $_errMsg . $content;
			} elseif (!empty ($_mailsendtError) AND $_showFormAgain === FALSE) {
				$content = $_errMsg;
			} else {
					//  get mail configuration
				$lConf     =& $this->conf['mail.'];
					//  send mail to advertiser
				$msgHeader = trim($this->piVars['sendertopic']) . ':' . chr(10) . $this->pi_getLL('mailSendNotice') . chr(10);
				$msgFooter = '

--

' . sprintf($this->pi_getLL('mailSendImprint'), $lConf['project']) . '

' . $lConf['company'] . '
' . $lConf['name'] . '
' . $lConf['street'] . '
' . $lConf['zip'] . ' ' . $lConf['city'] . '
' . $this->pi_getLL('mailSendImprintPhone') . ': ' . $lConf['phone'];
				$msg            = $msgHeader . t3lib_div::formatForTextarea($this->piVars['mailmsg']) . $msgFooter;
				$recipients     = $this->hiddenFields['fe_users.email'];
##				$cc             = $this->piVars['sendermail'];
				$bcc            = $lConf['bcc'];
				$email_from     = $lConf['no-reply'];
				$email_fromName = $lConf['company'];
				$replyTo        = $this->piVars['sendermail'];

				if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
					t3lib_div::devlog('MAIL: Content', $this->prefixId, 1, array(
						'msg' => $msg, 'recipients' => $recipients, 'cc' => $cc, 'email_from' => $email_from, 'email_fromName' => $email_fromName, 'replyTo' => $replyTo,
					));
				}

				$_subject = $lConf['project'] . ' - ' . $this->piVars['sendertopic'];
				if ($lConf['metaCharset'] == 'UTF-8' AND $lConf['charset'] != 'UTF-8') {
					$_subject = utf8_decode($_subject);
					$msg      = utf8_decode($msg);
				}
				$headers = 'From: ' . $email_fromName . ' <' . $email_from . '>
Reply-To: ' . $replyTo . '
Subject: ' . $_subject . '
Content-Type: text/plain; charset=' . $lConf['charset'] . '; format=flowed
Content-Transfer-Encoding: quoted-printable
';
				if (!empty ($cc)) {
					$headers .= 'Cc: ' . $cc . '
';
				}
				if (!empty ($bcc)) {
					$headers .= 'Bcc: ' . $bcc . '
';
				}
				if (@mail($recipients, $subject, wordwrap($msg, 70), $headers)) {
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 1) {
						t3lib_div::devlog('MAIL: sendt.', $this->prefixId, 1);
					}
					return $this->cObj->dataWrap($this->pi_getLL('mailSendSuccess'), $this->conf['success_sendMail_dataWrap']);
				} else {
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
						t3lib_div::devlog('MAIL: NOT sendt.', $this->prefixId, 3);
					}
					$markerArray['###MAILERROR###'] = $this->cObj->dataWrap($this->pi_getLL('mailSendError'), $this->conf['error_sendMail_dataWrap']);
				}
			}
		}

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * display Previewform and handle inserted data
	 *
	 * @return  HTML whole form
	 * @:TODO: translation, redirect target via TS; text modules to locallang.xml
	 * @:ToDo: include fakt only if is set in TS
	 * @access public
	 */
	public function getPreviewForm() {
		$content = '';

			//  wether the ad can be published or not
		$ready   = TRUE;

		$editPid = $this->conf['editPid'];
		$urlParameters = array(
			'tx_browser_pi1[showUid]' => $this->piVars['showUid'],
		);
		$editURL = $this->pi_getPageLink($editPid, $target='', $urlParameters);

		$publishPid = $GLOBALS['TSFE']->id;
		$urlParameters = array(
			'tx_browser_pi1[showUid]' => $this->piVars['showUid'],
			'no_cache' => 1,
		);
		$publishURL = $this->pi_getPageLink($publishPid, $target = '', $urlParameters);

			//  hide all unneeded elements during checkout
		##Aus USER_INT Objekten kann das pSetup Array nicht manipuliert werden.
		##$GLOBALS['TSFE']->pSetup['includeCSS.'][$this->extKey] = '';
        if (!empty ($this->conf['checkOutCSS'])) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" type="text/css" href="' . $this->conf['checkOutCSS'] . '" />';
        }

			/* invoice: show cart and store order(s) */
		if (empty ($_SESSION['tx_advertiser_pi3']['invoice']) OR empty ($_SESSION['tx_advertiser_pi3']['optionsselected'])) {
			$this->sessionCacheSelect();
		}

			## @ToDo: locallang/ html template
		if (!empty ($_SESSION['tx_advertiser_pi3']['invoice'])) {

				//  delete option from session if not selected by user
			foreach ($_SESSION['tx_advertiser_pi3']['optionsselected'] as $oKey => $oVal) {
				if (empty ($oVal)) {
					unset($_SESSION['tx_advertiser_pi3']['invoice']['options'][$oKey]);
				}
			}

				//  delete option from session if requestet
			if (!empty ($this->piVars['clearOption'])) {
					//  udpdate option in db
				$allowedOptions = array(
					'premium',
				);
				if (in_array($this->piVars['clearOption'], $allowedOptions)) {
						//  clear data in session
					unset($_SESSION['tx_advertiser_pi3']['invoice']['options'][$this->piVars['clearOption']]);
						//  store new session data in db:
					$this->sessionCacheUpdate();

						//  log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 0) {
						t3lib_div::devlog('preview form: option cleared: ' . $this->piVars['clearOption'], $this->prefixId, 0);
					}

						//  reset option in db entry
					$table         = $this->extKey_tx . '_ads';
					$where         = 'uid = ' . (int)$this->piVars['showUid'];
					$fields_values = array(
						$this->piVars['clearOption'] => 0,
					);
					$no_quote_fields = FALSE;
						//  log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
						$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
						t3lib_div::devlog('CLEAR OPTION: SQL query: ' . $sql, $this->prefixId, -1);
					}
					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
					$err = $GLOBALS['TYPO3_DB']->sql_error();
						//	log
					if (!empty ($err) AND $this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
						t3lib_div::devlog('CLEAR OPTION: SQL error: query: ' . $sql . '  || ERROR: ' . $err, $this->extKey, 3);
					}
				} else {
						//  log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
						t3lib_div::devlog('CLEAR OPTION: Wrong parameter: ' . htmlspecialchars($this->piVars['clearOption']), $this->prefixId, 3, $GLOBALS[TSFE]->fe_user->user);
					}
				}
			}

			$_SESSION['tx_advertiser_pi3']['orders'] = array();

			$payment =& $_SESSION['tx_advertiser_pi3']['payment'];
			$invoice =& $_SESSION['tx_advertiser_pi3']['invoice'];
			if ($payment[$invoice['dwelltime']]['type'] == 'creditconsumption') {
				$paymentMethod = 'Zahlung per Guthaben: ' . $payment[$invoice['dwelltime']]['amount'] . ' Punkt(e)';
				$_SESSION['tx_advertiser_pi3']['orders'][] = array(
					'type'         => 'creditconsumption',
					'description'  => 'Buchung Inserat # ' . $this->piVars['showUid'] . "\n" . 'Laufzeit: ' . $invoice['dwelltime'] . 'Tage' . "\n" . ' (Guthabenverbrauch)',
					'creditpoints' => $payment[$invoice['dwelltime']]['amount'],
					'credittype'   => $_SESSION['tx_advertiser_pi3']['userCredit']['type'],
				);
			} elseif ($payment[$invoice['dwelltime']]['type'] == 'trial') {
				$paymentMethod = 'Kostenfrei in Testphase: ' . $payment[$invoice['dwelltime']]['amount'] . ' €';
				$_SESSION['tx_advertiser_pi3']['orders'][] = array(
					'type'         => 'creditconsumption',
					'description'  => 'Buchung Inserat # ' . $this->piVars['showUid'] . "\n" . 'Laufzeit: ' . $invoice['dwelltime'] . 'Tage' . "\n" . ' (Guthabenverbrauch)',
					'creditpoints' => $payment[$invoice['dwelltime']]['amount'],
					'credittype'   => $_SESSION['tx_advertiser_pi3']['userCredit']['type'],
				);
			} else {
				$paymentMethod = 'Einzelzahlung: ' . $payment[$invoice['dwelltime']]['amount'] . ' €';
				$_SESSION['tx_advertiser_pi3']['orders'][] = array(
					'currency'     => '€',
					'amount'       => $payment[$invoice['dwelltime']]['amount'],
					'type'         => 'order',
					'description'  => 'Buchung Inserat # ' . $this->piVars['showUid'] . "\n" . 'Laufzeit: ' . $invoice['dwelltime'] . 'Tage' . "\n" . ' (Einzelzahlung)',
					'creditpoints' => 0,
					'credittype'   => 0,
				);

					//  retain needed payment
				$_SESSION['tx_advertiser_pi3']['invoice']['adamount'] = (float)$payment[$invoice['dwelltime']]['amount'];
			}
			$content .= '
		<div class="preview-form">
			<h2 class="csc-firstHeader">' . $this->pi_getLL('yourorder') . '</h2>
			<ul>
				<li><strong>Inserat # ' . $this->piVars['showUid'] . '</strong><br />
					' . $paymentMethod . '</li>
				<li><strong>Gewählte Laufzeit:</strong><br />
					' . $invoice['dwelltime'] . ' Tage</li>';

			if (!empty ($_SESSION['tx_advertiser_pi3']['invoice']['options'])) {
				foreach ($_SESSION['tx_advertiser_pi3']['invoice']['options'] as $oKey => $oVal) {
					$minDwelltime = 0;
					$n = 0;
					foreach ($_SESSION['tx_advertiser_pi3']['payment'] as $mdKey => $mdVal) {
						if ($n == 0) {
							$minDwelltime = $mdKey;
						}
						$n++;
					}
					$amount = $invoice['dwelltime'] / $minDwelltime * $oVal['value'];
##			    	$amountsSplitted = $this->amountSplit($amount);
					$_SESSION['tx_advertiser_pi3']['orders'][] = array(
						'currency'     => '€',
##						'amount'       => $amountsSplitted['amount'],
						'amount'       => $amount,
						'type'         => 'order',
						'description'  => 'Buchung Inserat # ' . $this->piVars['showUid'] . "\n" . '(' . $oVal['caption'] . ')' . "\n" . 'Laufzeit: ' . $invoice['dwelltime'] . 'Tage' . "\n" . ' (Einzelzahlung)',
						'creditpoints' => 0,
						'credittype'   => 0,
					);


					$content .= '
				<li><strong>Zusatzoption:</strong><br />
					' . $oVal['caption'] . '<br />
					<em>Betrag: ' . sprintf('%01.2f', $amount) . ' €</em><br />';

					$str            = 'Zusatzoption löschen';
					$urlParameters = array(
						$this->prefixId . '[clearOption]' => $oKey,
						'tx_browser_pi1[showUid]' => $this->currentRecord,
					);
					$cache          = 0;
					$altPageId      = 0;
					$delLink = $this->pi_linkTP($str, $urlParameters, $cache, $altPageId);
						## @:ToDo: conf to ts template
					$content .= $this->cObj->dataWrap($delLink, '<span class="link-delete-option">|</span>');

					$content .= '</li>';
				}
			}

			switch ($_SESSION['tx_advertiser_pi3']['adClassProperties']['vatincluded']) {
			case 'excluded':
				$vatinfo = 'Alle Preise zzgl. gesetzlicher MwSt.';
				break;
			case 'included':
				$vatinfo = 'Alle Preise inkl. gesetzlicher MwSt.';
				break;
			case 'zerovat':
				$vatinfo = 'Alle Preise sind umsatzsteuerbefreit gemäß § 19 Absatz 1 Satz 1 Umsatzsteuergesetz, UStG.';
				break;
			default:
				$vatinfo = '';
				break;
			}
			$content .= '
			</ul>
			<p>' . $vatinfo . '</p>';


				/*  FORM BANK DETAILS  */
			$invoiceOptions  =  count($_SESSION['tx_advertiser_pi3']['invoice']['options']);
			$invoiceAdAmount =& $_SESSION['tx_advertiser_pi3']['invoice']['adamount'];
			if ($invoiceOptions > 0 OR $invoiceAdAmount > 0) {
					//  load Fakt Basic object
				if (!empty ($this->conf['use_fakt_basic']) AND t3lib_extMgm::isLoaded('fakt_basic')) {
					require_once t3lib_extMgm::extPath('fakt_basic') . 'pi1/class.tx_faktbasic_pi1.php';
					$this->faktBasicObj    = t3lib_div::makeInstance('tx_faktbasic_pi1');
					$this->faktBasicLoaded = is_object($this->faktBasicObj);
					$this->faktBasicObj->main('', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_faktbasic_pi1.']);

						//	log
					if (!empty ($this->faktBasicLoaded) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 0) {
						t3lib_div::devlog('FAKT loaded', $this->prefixId, 0, array('faktInfo' => print_r($this->faktBasicObj, 1)));
					} elseif (empty ($this->faktBasicLoaded) AND $this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
						t3lib_div::devlog('ERROR FAKT loading failed', $this->prefixId, 3);
					}

						//	log
					if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 0) {
						t3lib_div::devlog('FAKT: call getBankdetailsForm', $this->prefixId, 0);
					}
					$bankDetails = $this->faktBasicObj->getBankdetailsForm($this);
					$ready       = $bankDetails['ready'];
					$content    .= $bankDetails['content'];
				}
			}


			$content .= '
		</div>';
		}

		$content .= '
		<div class="preview-form">
			<form class="helper-form" action="' . $editURL . '" method="post">
				<input type="submit" value="' . $this->pi_getLL('adEdit') . '" />
			</form>';

		if ($ready == TRUE) {
			$content .= '
			<p class="accept">' . nl2br($this->pi_getLL('adPublishAccept')) . '</p>
			<form class="helper-form" action="' . $publishURL . '" method="post">
				<input type="hidden" name="' . $this->prefixId . '[publish]" value="1" />
				<input type="submit" value="' . $this->pi_getLL('adPublish') . '" />
			</form>';

		}

		$content .= '
		</div>';

		if (!empty ($this->piVars['publish'])) {
				//  enabling this record
			$table         = $this->extKey_tx . '_ads';
			$where         = 'uid = ' . (int)$this->piVars['showUid'];
			$fields_values = array(
			##	'hidden' => 0,
				'ispreview' => 0,
			);
			$no_quote_fields = FALSE;
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
				t3lib_div::devlog('PUBLISH AD: SQL: QUERY: ' . $sql, $this->prefixId, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			$err = $GLOBALS['TYPO3_DB']->sql_error();
				//	log
			if (!empty ($err) AND $this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('PUBLISH AD: SQL error: QUERY: ' . $sql . '  || ERROR: ' . $err, $this->extKey, 3);
			}


				/* FAKT */
				//  @ToDo: add comments
			$user =& $GLOBALS[TSFE]->fe_user->user;
			$data =& $_SESSION['tx_advertiser_pi3']['payment'];
			$time =& $_SESSION['tx_advertiser_pi3']['dwelltime'];
			$params = array(
				'pid'           => $this->conf['fakt_basic.']['orders_pid'],
				'tstamp'        => time(),
				'crdate'        => time(),
				'debitor'       => $user['uid'],
				'debitornumber' => $user['tx_faktbasic_debitornumber'],
				'company'       => $user['company'],
				'salutation'    => $user['gender'] ? 'Frau' : 'Herr',
				'firstname'     => $user['first_name'],
				'lastname'      => $user['last_name'],
				'address'       => $user['address'],
				'zip'           => $user['zip'],
				'city'          => $user['city'],
				'country'       => $user['country'],
				'phone'         => $user['telephone'],
				'fax'           => $user['fax'],
				'mail'          => $user['email'],
			);

			if ($this->faktBasicLoaded === TRUE) {
				foreach ($_SESSION['tx_advertiser_pi3']['orders'] as $oVal) {
						//  split amounts if order (net, gross)
					if ($oVal['type'] == 'order') {
						$amountsSplitted = $this->amountSplit($oVal['amount']);
						$oVal['amount']      = $amountsSplitted['amount'];
						$oVal['vatincluded'] = $_SESSION['tx_advertiser_pi3']['adClassProperties']['vatincluded'];
						$oVal['vatrate']     = $_SESSION['tx_advertiser_pi3']['adClassProperties']['vatrate'];
						$oVal['amounttotal'] = $amountsSplitted['amounttotal'];
					}
					$params = array_merge($params, $oVal);
					$orderID = $this->faktBasicObj->setOrder($params);
							//	log
					if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
						t3lib_div::devlog('FAKT::setOrder(): given data', $this->extKey, -1, $params);
					}


						//  send admin mail order
					$markerArray = array(
						'###ORDER_ID###'         => $orderID,
						'###ADVERTISER_NAME###'  => $GLOBALS['TSFE']->fe_user->user['name'],
						'###ADVERTISER_USERNAME' => $GLOBALS['TSFE']->fe_user->user['username'],
						'###DESCRIPTION###'      => $params['description'],
					);
					if ($oVal['type'] == 'order') {
						$markerArray['###AMOUNT###']      = $params['amount'];
						$markerArray['###VATINCLUDED###'] = $vatinfo;
						$markerArray['###VATRATE###']     = $params['vatrate'];
						$markerArray['###AMOUNTTOTAL###'] = $params['amounttotal'];
						$this->faktBasicObj->adminMail($oVal['type'], $markerArray);
/**
 * @:ToDo:
 * -    adminMail only if is enabled in TS
 * -    adminMail creditconsumption always (if is enabled in TS), not only if there is an order
 */
##					} elseif ($oVal['type'] == 'creditconsumption') {
##						$markerArray['###CREDIT_TYPE###']   = $params['credittype'];
##						$markerArray['###CREDIT_POINTS###'] = $params['creditpoints'];
##						$this->faktBasicObj->adminMail($oVal['type'], $markerArray);
					}
				}
			}

##			unset($_SESSION['tx_advertiser_pi3']['recordUid']);
			unset($_SESSION['tx_advertiser_pi3']);
			$this->sessionCacheDelete();


				//  redirect
			$id       = $this->conf['userListPid'];
			$target   = '';
			$urlParameters = array();
			$location = $this->pi_getPageLink($id, $target, $urlParameters);
			$location = t3lib_div::locationHeaderUrl($location);
				//  log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
				t3lib_div::devlog('REDIRECT: target is ' . $location, $this->prefixId, -1);
			}

			header('Location: ' . $location);
			exit;
		}

##$content.= '<pre><b><u>$_SESSION[tx_advertiser_pi3]:</u></b> ' . print_r($_SESSION['tx_advertiser_pi3'], 1) . '</pre>';
##$content.= '<pre><b><u>$GLOBALS[TSFE]->fe_user->user:</u></b> ' . print_r($GLOBALS['TSFE']->fe_user->user, 1) . '</pre>';
##$content.= '<pre><b><u>$this->piVars:</u></b> ' . print_r($this->piVars, 1) . '</pre>';

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * parse diffenrece between today and timestamp from db as number of years/ months past
	 *
	 * @param	double	$amount: raw amount to be splitted
	 * @return	array   $amounts: net and gross amount
	 * @access public
	 */
	public function amountSplit($amount) {
			//  calculate VAT dependend valus
		$vatInfo = $_SESSION['tx_advertiser_pi3']['adClassProperties'];
		switch ($vatInfo['vatincluded']) {
		case 'included':
			$amounttotal = $amount;
			$amount      = $amount * 100 / (100 + $vatInfo['vatrate']);
			$amount      = round($amount, 2);
			$vat         = $amounttotal - $amount;
			break;
		case 'excluded':
			$amount      = $amount;
			$vat         = $amount * $vatInfo['vatrate'];
			$vat         = round($vat, 2);
			$amounttotal = $amount + $vat;
			break;
		default:
 			$amount      = $amount;
			$amounttotal = $amount;
			break;
		}

		$amounts = array(
			'amount'      => $amount,
			'amounttotal' => $amounttotal,
		);
			//  log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('AMOUNTSPLIT: result', $this->prefixId, -1, $amounts);
		}

		return $amounts;
	}

	// -------------------------------------------------------------------------
	/**
	 * parse diffenrece between today and timestamp from db as number of years/ months past
	 *
	 * @param	string	$value: Name of field in local table containing the timestamp
	 * @param	string	$formatY: format string for year value used by sprintf()
	 * @param	string	$formatM: format string for month value used by sprintf()
	 * @return	string formatted data
	 * @access public
	 */
	public function parseDate($value, $formatY, $formatM) {
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: raw value: ' . $value, $this->extKey, -1);
		}
		$value = time() - $value;
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: diff value: ' . $value, $this->extKey, -1);
		}
			//  parse it
		$result = array();
		$secondsInAYear = 365 * 24 * 60 * 60;
		$secondsInAMonth = 30 * 24 * 60 * 60;

			//  number of years:
		$years = floor($value / $secondsInAYear);
		if ($years > 0) {
			$result[] = sprintf($formatY, $years);
		}
			//  number of months:
		$months = $value - ($years * $secondsInAYear);
		$months = floor($months / $secondsInAMonth);
		if ($months > 0) {
			$result[] = sprintf($formatM, $months);
		}
			//  complete str
		$result = implode(' / ', $result);
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			t3lib_div::devlog('PARSE DATE: result: ' . $result, $this->extKey, -1);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	/**
	 * :TODO: desc
	 *
	 * @return	void
	 * @obsolete
	 */
	function sessionCacheSelect($returnOnlyUid = FALSE) {
			//	get freshest session content record for this ad
		$select_fields = 'uid, sessioncontent';
		$from_table    = $this->extKey_tx . '_sessioncache';
		$where_clause  = 'ad = ' . (int)$this->piVars['showUid'];
		$where_clause .= $this->cObj->enableFields($from_table, $show_hidden = 0);
		$groupBy       = '';
		$orderBy       = 'tstamp DESC';
		$limit         = '0, 1';
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			t3lib_div::devlog('SESSION CACHE read: SQL query: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
		if (!empty ($err)) {
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('SESSION CACHE read: SQL error: ' . $err, $this->prefixId, 3);
			}
		}
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
		    $ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		    if ($returnOnlyUid === TRUE) {
		    	return $ftc['uid'];
		    } else {
				$sessioncontent = unserialize($ftc['sessioncontent']);
	            $_SESSION['tx_advertiser_pi3']['adClassProperties'] = $sessioncontent['adClassProperties'];
	            $_SESSION['tx_advertiser_pi3']['invoice']           = $sessioncontent['invoice'];
	            $_SESSION['tx_advertiser_pi3']['optionsselected']   = $sessioncontent['optionsselected'];
	            $_SESSION['tx_advertiser_pi3']['payment']           = $sessioncontent['payment'];
            }
		}
	}

	// -------------------------------------------------------------------------
	/**
	 * :TODO: desc
	 *
	 * @return	void
	 * @obsolete
	 */
	function sessionCacheUpdate() {
			//	get uid of freshest session content record for this ad
		$uid            = $this->sessionCacheSelect($returnOnlyUid = TRUE);
		$sessioncontent = array(
			'adClassProperties' => $_SESSION['tx_advertiser_pi3']['adClassProperties'],
			'invoice'           => $_SESSION['tx_advertiser_pi3']['invoice'],
			'optionsselected'   => $_SESSION['tx_advertiser_pi3']['optionsselected'],
			'payment'           => $_SESSION['tx_advertiser_pi3']['payment'],
		);
		$sessioncontent = serialize($sessioncontent);

		$table           = $this->extKey_tx . '_sessioncache';;
		$where           = 'uid = ' . (int)$uid;
		$fields_values   = array(
		    'tstamp'         => time(),
		    'sessioncontent' => $sessioncontent,
		);
		$no_quote_fields = FALSE;
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			t3lib_div::devlog('SESSION CACHE update: SQL query: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
		if (!empty ($err)) {
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('SESSION CACHE update: SQL error: ' . $err, $this->prefixId, 3);
			}
		}
	}

	// -------------------------------------------------------------------------
	/**
	 * :TODO: desc
	 *
	 * @return	void
	 * @obsolete
	 */
	function sessionCacheDelete() {
			//	set all session content record for this ad as deleted
		$table           = $this->extKey_tx . '_sessioncache';;
		$where           = 'ad = ' . (int)$this->piVars['showUid'];
		$fields_values   = array(
		    'tstamp'  => time(),
		    'deleted' => 1,
		);
		$no_quote_fields = FALSE;
			//	log
		if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= -1) {
			$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			t3lib_div::devlog('SESSION CACHE delete: SQL query: ' . $sql, $this->prefixId, -1);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
		if (!empty ($err)) {
				//	log
			if ($this->conf['log_on'] == 1 AND $this->conf['log_mode'] <= 3) {
				t3lib_div::devlog('SESSION CACHE delete: SQL error: ' . $err, $this->prefixId, 3);
			}
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi1/class.tx_advertiser_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi1/class.tx_advertiser_pi1.php']);
}
?>