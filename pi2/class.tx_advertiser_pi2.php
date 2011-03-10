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
 * Plugin 'Advertiser: Data edit (picture upload)' for the 'advertiser' extension.
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_advertiser
 */
class tx_advertiser_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_advertiser_pi2';                // Same as class name
	var $scriptRelPath = 'pi2/class.tx_advertiser_pi2.php';  // Path to this script relative to the extension dir.
	var $extKey        = 'advertiser';                       // The extension key.
	var $extKey_tx     = 'tx_advertiser';                    // The prefixed extension key.
	var $pi_checkCHash = true;

	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content, $conf) {
			//  prepare plugin config
		$conf['plugin']	=& unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['advertiser']);
		if (empty ($conf['upload'])) {
			$conf['upload'] =  'uploads/' . $this->extKey_tx . '/';  //  :TODO: move to ts
		}
			//  check for trailing slash
		if (!preg_match('%\/$%', $conf['upload'])) {
		    $conf['upload'] .= '/';
		}

			//  prepare plugin config
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

			//  get path to extension dir
		$_extPath = strtr(__FILE__, array('\\' => '/'));
		$_extPath = strtr($_extPath, array($this->scriptRelPath => ''));
		$_extHref = strtr($_extPath, array(PATH_site => ''));
		$this->extPath = $_extPath;
		$this->extHref = $_extHref;

			//	current record: get from piVars of plugin: tx_browser_pi1
		$this->recordUid = (int)$_SESSION['tx_advertiser_pi3']['recordUid'];

			//	load current records data
		$this->getData();
		if (!empty ($this->piVars['process']) AND $this->piVars['process'] == 1) {
				//  edit data
			$content .= $this->setData();

				//  ready?
			if ($this->ready === true) {
			    $this->redirect2Preview();
					//  log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
					t3lib_div::devlog('READY? yes', $this->extKey, -1, array($_FILES, $this->piVars));
				}
			} else {
					//  log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
					t3lib_div::devlog('READY? no', $this->extKey, -1, array($_FILES, $this->piVars));
				}
			}

			$this->getData();
		}
		$content = $this->renderForm($content);

		return '<div class="tx-arj-2">' . $content . '</div>';
	}

	// -------------------------------------------------------------------------
	/**
	 * Single line description of function getRecord.
	 *
	 * Multi line
	 * description
	 * of function getRecord.
	 *
	 * @return array	$images
	 */
	function getData() {
		$select_fields = 'image';
		$from_table    = $this->extKey_tx . '_ads';
		$where_clause  = 'uid = ' . $this->recordUid;
		$groupBy       = '';
		$orderBy       = '';
		$limit         = 1;
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('SQL: QUERY:<br />' . $sql, $this->extKey, -1, $_SESSION);
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$err = $GLOBALS['TYPO3_DB']->sql_error($res);
		$images = array();
		if ($err) {
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
				t3lib_div::devlog('SQL: ERROR:<br />' . $err, $this->extKey, 3);
			}
		} else {
				//  fetch current record
			$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if (!empty ($ftc['image'])) {
				$images = explode(',', $ftc['image']);
			} else {
				$images = null;
			}
		}

		$this->images = $images;
	}

	// -------------------------------------------------------------------------
	/**
	 * Single line description of function getRecord.
	 *
	 * Multi line
	 * description
	 * of function getRecord.
	 *
	 * @return string	   ?
	 */
	function setData() {
		$content     = '';
		$images	     =& $this->images;
		$this->ready = true;  //  if is show this form again

			//	delete images
		$images_delete = array();
		if (!empty ($this->piVars['image_delete']) AND is_array($this->piVars['image_delete'])) {
			$this->ready = false;
				//	collect images to be deleted
			foreach ($this->piVars['image_delete'] as $dVal) {
				$images_delete[] = array_search($dVal, $images);
			}
		}

			//  if there ar images to be deleted remove from db value, remove img file
		foreach ($images_delete as $diVal) {
		    $_file = $this->conf['upload'] . $images[(int)$diVal];
			$res   = @unlink($_file);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('UNLINK: ' . $_file . ' (' . (int)$res . ')', $this->extKey, -1);
			}

			unset($images[$diVal]);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('UNSET: New value', $this->extKey, -1, $images);
			}
		}

			//  add images
			//  :TODO: see http://www.pi-phi.de/177.html
			//	collect images to be added
		$image_upload = array();
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('FILES', $this->extKey, -1, $_FILES);
		}
		if (!empty ($_FILES) AND count($_FILES) > 0 AND count($this->images) < $this->conf['maxImages']) {
			foreach ($_FILES['tx_advertiser_pi2']['error']['image_upload'] as $fKey => $fVal) {
				//  :TODO: catch and display errors
				if ($fVal == 0) {  //  consider only UPLOAD_ERR_OK
					$image_upload[$fKey] = array(
						'name'     => $_FILES['tx_advertiser_pi2']['name']['image_upload'][$fKey],
						'tmp_name' => $_FILES['tx_advertiser_pi2']['tmp_name']['image_upload'][$fKey],
						'img_size' => getimagesize($_FILES['tx_advertiser_pi2']['tmp_name']['image_upload'][$fKey]),
					);
				}
			}
		}
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('NEW FILE image_upload?', $this->extKey, -1, $image_upload);
		}

		if (count($image_upload) > 0) {
		    $this->ready    = false;
			$this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');

				//  check for valid image format and move uploades file
			foreach ($image_upload as $uKey => $uVal) {
					//  log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
					t3lib_div::devlog('NEW FILE image?', $this->extKey, -1, $uVal['img_size']);
				}
				##if (!is_array($uVal['img_size'] OR ($uVal['img_size'][2] < 1) OR ($uVal['img_size'][2] > 3))) {
				if (!is_array($uVal['img_size'])) {
						//  sort out invalid image format
						if ($uVal['img_size'][2] < 1 OR $uVal['img_size'][2] > 3) {
							continue;
						}
						//  log
					if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
						t3lib_div::devlog('NEW FILE sort out: ' . $uVal['name'] . ' (' . $uVal['img_size'][2] . ')', $this->extKey, -1);
					}
					continue;
				}
					//  move uploaded file
				$source      = $uVal['tmp_name'];
				$destination = $this->fileFunc->cleanFileName($uVal['name']);
				$destination = strtolower($destination);
				$destination = $this->fileFunc->getUniqueName($destination, $this->conf['upload'], $dontCheckForUnique = 0);
					//  log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
					t3lib_div::devlog('NEW FILE destination: ' . $destination, $this->extKey, -1);
				}
				$images[]    = strtr($destination, array($this->conf['upload'] => ''));
				t3lib_div::upload_copy_move($source, $destination);
			}
		}
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('Ready? ' . (int)$this->ready, $this->extKey, -1);
			t3lib_div::devlog('UPDATE: current images', $this->extKey, -1, $images);
		}

		if ($this->ready === false) {
			$table           = $this->extKey_tx . '_ads';
			$where           = 'uid = ' . $this->recordUid;
			$fields_values   = array(
				'image' => implode(',', $images),
			);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('SQL: UPDATE fields ', $this->extKey, -1, $fields_values);
			}
			$no_quote_fields = false;
			$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
				//  log
			if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
				t3lib_div::devlog('SQL: UPDATE:<br />' . $sql, $this->extKey, -1);
			}
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
			$err = $GLOBALS['TYPO3_DB']->sql_error($res);
			if ($err) {
					//  log
				if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= 3) {
					t3lib_div::devlog('SQL: UPDATE:<br />' . $err, $this->extKey, 3);
				}
			}
		}

		return $content;
	}

	// -------------------------------------------------------------------------
	/**
	 * Single line description of function getRecord.
	 *
	 * Multi line
	 * description
	 * of function getRecord.
	 *
	 * @return string	   ?
	 * @:TODO: text to locallang
	 * @:TODO: html to template
	 */
	function renderForm($content) {
		$images =& $this->images;
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('FORM: given Data', $this->extKey, -1, $ftc);
		}

			//  start form
		##$_action  = $this->pi_getPageLink($id = $GLOBALS['TSFE']->id, $target = '', $urlParameters = array('no_cache' => 1));
        $_action  = $this->pi_getPageLink($id = $GLOBALS['TSFE']->id, $target = '', $urlParameters = array());
		$content .= '
				<form action="' . $_action . '" method="post" enctype="multipart/form-data">
					<input type="hidden" name="tx_advertiser_pi2[process]" value="1" />';

			//  images
		if (!is_null($images)) {
			$numImg = count($images);
			if ($numImg > 0) {
				$content .= '
				<p>Hier sehen Sie die Grafiken, die bereits zu diesem Inserat hinzugefügt wurden.<br />
				  Möchten Sie Grafiken löschen, markieren Sie diese bitte mit dem entsprechenden Häkchen und klicken Sie auf "Weiter":</p>';

				foreach ($images as $iKey => $iVal) {
					$images[$iKey] = trim($images[$iVal]);
					$iConf['file'] = $this->conf['upload'] . $iVal;
					$iConf['file.']['maxW'] = '140';
					$iConf['file.']['maxH'] = '140';
					$iRsrc = $this->cObj->IMG_RESOURCE($iConf);
						//  log
					if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
						$arr = array(
							$iConf, $GLOBALS['TSFE']->lastImageInfo,
						);
						t3lib_div::devlog('FORM: lastImageInfo' . $err, $this->extKey, -1, $arr);
					}

### uherrmann 2011-03-10: avoid 'advertiser' in img src:
### @todo: make it configurable in TS
##					$content .= '
##					<div class="image-container">
##						<input type="checkbox" name="tx_advertiser_pi2[image_delete][]" value="' . $iVal . '" class="ip-checkbox" />
##						<img src="' . $this->extHref . 'pi2/imagecontainer.gif" class="image" style="background-image: url(' . $iRsrc . ');" />
##					</div>';
					$content .= '
					<div class="image-container">
						<input type="checkbox" name="tx_advertiser_pi2[image_delete][]" value="' . $iVal . '" class="ip-checkbox" />
						<img src="/clear.gif" class="image" style="background-image: url(' . $iRsrc . '); border: 1px solid rgb(211,211,211); height: 150px; width: 150px;" />
					</div>';
				}

				$content .= '
				<div class="cl"></div>';
			}
		}

		if ($numImg < $this->conf['maxImages']) {
			$content .= '
				<p>Hier können Sie bis zu sechs Grafiken insgesamt zu diesem Inserat hinzufügen<br />
				  (erlaubte Formate: JPG/GIF/PNG):</p>
				<p>Die erste Grafik wird in den Listendarstellungen (Übersichtsseiten mit mehreren Inseraten untereinander) verwendet.</p>
				<p>Bitte beachten Sie: Je nach Geschwindigkeit Ihres Internetanschlusses kann das Hochladen einige Minuten dauen – insbesondere, wenn Sie mehrere Fotos in Originalgröße einstellen. Bitte brechen Sie den Vorgang nicht ab!</p>';

			for ($i = $numImg; $i < $this->conf['maxImages']; $i++) {
				$content .= '
					<input type="file" name="tx_advertiser_pi2[image_upload][]" class="ip-file" />';
			}
		}

			//	finish form
		$content .= '
					<input type="submit" value="Weiter" class="button" onclick="document.getElementById(\'ajax_loader\').style.display = \'block\';" />
					<div id="ajax_loader" style="display: none;">
                        <img src="' . $this->extHref . 'pi2/ajax-loader.circle-thickbox.gif" border="0" width="100" height="100" alt="loading..." />
					</div>
				</form>';

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * Single line description of function getRecord.
	 *
	 * Multi line
	 * description
	 * of function getRecord.
	 *
	 * @return string	   ?
	 */
	function redirect2Preview() {
		$baseURL       = $GLOBALS['TSFE']->config['config']['baseURL'];
		//  check for trailing slash
		if (!preg_match('%\/$%', $baseURL)) {
		    $baseURL .= '/';
		}

			### :TODO: use T3_lib_div instead
		$id            = (int)$this->conf['redirect'];
		$urlParameters = array();
		$pageLink      = $baseURL . $this->pi_getPageLink($id, $target='', $urlParameters);
			//  log
		if ($this->conf['plugin']['log_on'] == 1 AND $this->conf['plugin']['log_mode'] <= -1) {
			t3lib_div::devlog('REDIRECT', $this->extKey, -1, array('targetPid' => $id, 'target' => $pageLink));
		}

		header('Location: ' . $pageLink);
		exit;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi2/class.tx_advertiser_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advertiser/pi2/class.tx_advertiser_pi2.php']);
}
?>