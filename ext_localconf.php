<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_ads=1
');
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_advertiser_ads", field "description"
	# ***************************************************************************************
RTE.config.tx_advertiser_ads.description {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_manufacturers=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_states=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_productgroups=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_categories=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_payment=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_dispatch=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_price_options=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_vat=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_classes=1
');
/* ToDo: delete
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_dwelltime=1
');
*/
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advertiser_credittypes=1
');



//  Here you register evaluation method in the $TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals'] array.
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_advertiser_evalfield_price'] = 'EXT:advertiser/classes/class.tx_advertiser_evalfield_price.php';





t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_advertiser_pi1.php', '_pi1', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_advertiser_pi2.php', '_pi2', 'list_type', 0);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_advertiser_pi3.php', '_pi3', 'list_type', 0);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi4/class.tx_advertiser_pi4.php', '_pi4', 'list_type', 0);

if (TYPO3_MODE=='FE'){
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_feuser_register']['extendingTCA'][] = 'advertiser';
}
?>