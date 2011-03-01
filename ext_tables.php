<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_advertiser_classes'] = array (
	'ctrl' => array (
		'title'                    => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes',
		'label'                    => 'title',
		'label_alt'                => 'fe_group_access',
		'label_alt_force'          => 1,
		'canNotCollapse'           => 1,
		'tstamp'                   => 'tstamp',
		'crdate'                   => 'crdate',
		'cruser_id'                => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'sortby'                   => 'sorting',
		'delete'                   => 'deleted',
		'enablecolumns'            => array (
			'disabled' => 'hidden',
			'starttime'=> 'starttime',
			'endtime'  => 'endtime',
		),
		'dynamicConfigFile'        => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'                 => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
		'requestUpdate'            => 'fe_group_accesstype,vatincluded,template_select',
	),
);

$TCA['tx_advertiser_ads'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads',
		'label'             => 'uid',
		'label_alt'         => 'title',
		'label_alt_force'   => 1,
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'cruser_id'         => 'cruser_id',
		'type'              => 'type',
		'default_sortby'    => 'ORDER BY crdate DESC',
		'delete'            => 'deleted',
		'enablecolumns'     => array (
			'disabled'  => 'hidden',
			'starttime' => 'starttime',
			'endtime'   => 'endtime',
		),
		'typeicon_column'   => 'type',
		'typeicons'         => Array (
			'1'                => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/ads_req.gif',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
		'dividers2tabs'     => 1,
	),
);

$TCA['tx_advertiser_manufacturers'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_manufacturers',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_states'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_states',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_productgroups'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_productgroups',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_categories'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_categories',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_payment'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_payment',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_dispatch'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dispatch',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_price_options'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_price_options',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_vat'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_vat',
		'label'             => 'title',
		'label_alt'         => 'country',
		'label_alt_force'   => 1,
		'tstamp'            => 'tstamp',
		'crdate'            => 'crdate',
		'cruser_id'         => 'cruser_id',
		'sortby'            => 'sorting',
		'delete'            => 'deleted',
		'enablecolumns'     => array (
			'disabled'  => 'hidden',
			'starttime' => 'starttime',
			'endtime'   => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

/* ToDo: delete
$TCA['tx_advertiser_dwelltime'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dwelltime',
		'label'     => 'dwelltime',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
##		'default_sortby' => 'ORDER BY dwelltime',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled'  => 'hidden',
			'starttime' => 'starttime',
			'endtime'   => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);
*/

$TCA['tx_advertiser_credittypes'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_credittypes',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby'    => 'sorting',
		'delete'    => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
	),
);

$TCA['tx_advertiser_sessioncache'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_sessioncache',
		'label'     => 'ad',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete' => 'deleted',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/entry_icon.gif',
		'readOnly'  => 1,
	),
);




t3lib_extMgm::addStaticFile($_EXTKEY, 'static/advertiser_pi1/', 'Advertiser: Advanced single view / pi1');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/advertiser_pi3/', 'Advertiser: Ads class selector / pi3');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/browser/',        'Advertiser: Default Browser Config');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/bids/',           'Advertiser: Browser listView bids');
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/requests/',       'Advertiser: Browser listView requests');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advertiser/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/ext_icon_pi1.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advertiser/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/ext_icon_pi2.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';


// $_EXTKEY.pi3: Add flexform field to plugin options
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3'] = 'pi_flexform';
// $_EXTKEY.pi3: Add flexform DataStructure (flexform file depends on the value of the setting useReference)
$advertiserConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['advertiser']);
//change flexform file depending on the value of the setting useReference
if ($advertiserConfig['useReference'] == 0) {
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:'.$_EXTKEY.'/pi3/flexform_pi3.xml');
} else {
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:'.$_EXTKEY.'/pi3/flexform_pi3.ref.xml');
}

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advertiser/locallang_db.xml:tt_content.list_type_pi3',
	$_EXTKEY . '_pi3',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/ext_icon_pi3.gif'
),'list_type');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi4']='layout,select_key,pages';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advertiser/locallang_db.xml:tt_content.list_type_pi4',
	$_EXTKEY . '_pi4',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/ext_icon_pi4.gif'
),'list_type');

$tempColumns = array (
	'tx_advertiser_accept_gtc' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:advertiser/locallang_db.xml:fe_users.tx_advertiser_accept_gtc',
		'config' => array (
			'type' => 'check',
		)
	),
	/* löschen */
	'tx_advertiser_merchant' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:advertiser/locallang_db.xml:fe_users.tx_advertiser_merchant',
		'config' => array (
			'type' => 'check',
		)
	),
	/* /löschen */
	'tx_advertiser_vat_id' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:advertiser/locallang_db.xml:fe_users.tx_advertiser_vat_id',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','--div--;LLL:EXT:advertiser/locallang_db.xml:fe_users.tx_advertiser_divlabel,tx_advertiser_accept_gtc,tx_advertiser_merchant;;;;1-1-1,tx_advertiser_advertiser_type;;;;1-1-1,tx_advertiser_vat_id;;;;2-2-2');t3lib_div::loadTCA('fe_users');


$tempColumns = array (
	'zn_name_local_lang_ol' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:advertiser/locallang_db.xml:static_country_zones.tx_advertiser_zn_name_local_lang_ol',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
);

t3lib_div::loadTCA('static_country_zones');
t3lib_extMgm::addTCAcolumns('static_country_zones',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('static_country_zones','tx_advertiser_zn_name_local_lang_ol;;;;1-1-1');


$tempColumns = array (
	'title_lang_ol' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:advertiser/locallang_db.xml:fe_groups.tx_advertiser_fe_groups_title_lang_ol',
		'config' => array (
			'type' => 'input',
			'size' => '30',
		)
	),
);

t3lib_div::loadTCA('fe_groups');
t3lib_extMgm::addTCAcolumns('fe_groups',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_groups','title_lang_ol;;;;1-1-1', '', 'after:title');


	// initalize "context sensitive help" (csh)
t3lib_extMgm::addLLrefForTCAdescr('tx_advertiser_classes','EXT:advertiser/res/locallang/locallang_csh_tx_advertiser_classes.xml');
?>