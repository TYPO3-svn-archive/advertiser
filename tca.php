<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


	// get extension confArr
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['advertiser']);
$defaultVatRate = $confArr['defaultVatRate'] ? $confArr['defaultVatRate'] : '';


$TCA['tx_advertiser_ads'] = array(
	'ctrl' => $TCA['tx_advertiser_ads']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,type,product_group,manufacturer,title,description,category,state,dateofproduction,warranty,image,productlink,price,vat,price_option,shipping,dispatch,payment,fe_user,premium,sold,remaining_term,class,ispreview',
	),
	'feInterface' => $TCA['tx_advertiser_ads']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0',
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range' => array(
					'upper' => mktime(3,
					14,
					7,
					1,
					19,
					2038),
					'lower' => mktime(0,
					0,
					0,
					date('m') - 1,
					date('d'),
					date('Y')),
				),
			),
		),
		'type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.type.I.0',
						'0',
						t3lib_extMgm::extRelPath('advertiser').'res/icons/selicon_tx_advertiser_ads_type_0.gif',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.type.I.1',
						'1',
						t3lib_extMgm::extRelPath('advertiser').'res/icons/selicon_tx_advertiser_ads_type_1.gif',
					),
				),
				'size' => 1,
				'maxitems' => 1,
			),
		),
		'product_group' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.product_group',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_productgroups',
				'foreign_table_where' => 'AND tx_advertiser_productgroups.pid=###STORAGE_PID### AND tx_advertiser_productgroups.deleted = 0 AND tx_advertiser_productgroups.hidden = 0 ORDER BY tx_advertiser_productgroups.title',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_productgroups',
							'pid'      => '###STORAGE_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'manufacturer' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.manufacturer',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_manufacturers',
				'foreign_table_where' => 'AND tx_advertiser_manufacturers.pid=###STORAGE_PID### AND tx_advertiser_manufacturers.deleted = 0 AND tx_advertiser_manufacturers.hidden = 0 ORDER BY tx_advertiser_manufacturers.title',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_manufacturers',
							'pid'      => '###STORAGE_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.description',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			),
		),
		'category' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.category',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_categories',
				'foreign_table_where' => 'AND tx_advertiser_categories.deleted = 0 AND tx_advertiser_categories.hidden = 0 ORDER BY tx_advertiser_categories.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_categories',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'state' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.state',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_states',
				'foreign_table_where' => 'AND tx_advertiser_states.deleted = 0 AND tx_advertiser_states.hidden = 0 ORDER BY tx_advertiser_states.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_states',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'dateofproduction' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.dateofproduction',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0',
			),
		),
		'warranty' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.warranty',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'eval' => 'int,nospace',
			),
		),
		'image' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.image',
			'config' => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder'  => 'uploads/tx_advertiser',
				'show_thumbs'   => 1,
				'size'          => 3,
				'minitems'      => 0,
				'maxitems'      => 10,
			),
		),
		'productlink' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.productlink',
			'config' => array(
				'type'     => 'input',
				'size'     => '30',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'price' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.price',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'eval' => 'double2,nospace',
			),
		),
		'vat' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.vat',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_advertiser_vat',
				'foreign_table_where' => 'AND tx_advertiser_vat.deleted = 0 AND tx_advertiser_vat.hidden = 0 ORDER BY tx_advertiser_vat.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
			),
		),
		'price_option' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.price_option',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_advertiser_price_options',
				'foreign_table_where' => 'AND tx_advertiser_price_options.deleted = 0 AND tx_advertiser_price_options.hidden = 0 ORDER BY tx_advertiser_price_options.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_price_options',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'shipping' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.shipping',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'eval' => 'double2,nospace',
			),
		),
		'dispatch' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.dispatch',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_dispatch',
				'foreign_table_where' => 'AND tx_advertiser_dispatch.deleted = 0 AND tx_advertiser_dispatch.hidden = 0 ORDER BY tx_advertiser_dispatch.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_dispatch',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'payment' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.payment',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_advertiser_payment',
				'foreign_table_where' => 'AND tx_advertiser_payment.deleted = 0 AND tx_advertiser_payment.hidden = 0 ORDER BY tx_advertiser_payment.sorting',
				'size'                => 4,
				'minitems'            => 0,
				'maxitems'            => 4,
				"MM"                  => "tx_advertiser_ads_payment_mm",
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'  => 'script',
						'title' => 'Create new record',
						'icon'  => 'add.gif',
						'params' => array(
							'table'    => 'tx_advertiser_payment',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend',
						),
						'script' => 'wizard_add.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			),
		),
		'fe_user' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.fe_user',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'fe_users',
				'foreign_table_where' => 'AND fe_users.deleted = 0 AND fe_users.disable = 0 ORDER BY fe_users.username',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
			),
		),
		'premium' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.premium',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.premium.I.0',
						'0',
						t3lib_extMgm::extRelPath('advertiser').'res/icons/selicon_tx_advertiser_ads_premium_0.gif',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.premium.I.1',
						'1',
						t3lib_extMgm::extRelPath('advertiser').'res/icons/selicon_tx_advertiser_ads_premium_1.gif',
					),
				),
				'size' => 1,
				'maxitems' => 1,
			),
		),
		'sold' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.sold',
			'config' => array(
				'type' => 'check',
			),
		),
		'remaining_term' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.remaining_term',
			'config' => array (
				'type' => 'input',
				'size' => '8',
				'eval' => 'int,nospace',
			)
		),

		'class' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.class',
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_advertiser_classes',
##				'foreign_table_where' => 'AND tx_advertiser_classes.pid=###STORAGE_PID### ORDER BY tx_advertiser_classes.sorting',
				'foreign_table_where' => 'ORDER BY tx_advertiser_classes.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'ispreview' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.ispreview',
			'config' => array(
				'type' => 'check',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, type;;2, product_group, manufacturer,
							--div--;LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.div_description,
							title;;;;2-2-2, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_advertiser/rte/];3-3-3, productlink, category;;5,
							--div--;LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.div_price,
							price;;3, shipping;;4, payment,
							--div--;LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.div_images,
							image',
		),
		'1' => array(
			'showitem' => 'hidden;;1;;1-1-1, type;;2, product_group, manufacturer,
							--div--;LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.div_description,
							title;;;;2-2-2, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_advertiser/rte/];3-3-3, category, price,
							--div--;LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_ads.div_images,
							image,',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => 'starttime, endtime, remaining_term, sold, ispreview',
		),
		'2' => array(
			'showitem' => 'fe_user, class, premium',
		),
		'3' => array(
			'showitem' => 'vat, price_option',
		),
		'4' => array(
			'showitem' => 'dispatch',
		),
		'5' => array(
			'showitem' => 'state, dateofproduction, warranty',
		),
	),
);



$TCA['tx_advertiser_manufacturers'] = array(
	'ctrl' => $TCA['tx_advertiser_manufacturers']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol,image',
	),
	'feInterface' => $TCA['tx_advertiser_manufacturers']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_manufacturers.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_manufacturers.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'image' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_manufacturers.image',
			'config' => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder'  => 'uploads/tx_advertiser',
				'show_thumbs'   => 1,
				'size'          => 1,
				'minitems'      => 0,
				'maxitems'      => 1,
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;2-2-2, image;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_states'] = array(
	'ctrl' => $TCA['tx_advertiser_states']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_states']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_states.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_states.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_productgroups'] = array(
	'ctrl' => $TCA['tx_advertiser_productgroups']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_productgroups']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_productgroups.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_productgroups.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_categories'] = array(
	'ctrl' => $TCA['tx_advertiser_categories']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_categories']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_categories.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_categories.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_payment'] = array(
	'ctrl' => $TCA['tx_advertiser_payment']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_payment']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_payment.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_payment.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_dispatch'] = array(
	'ctrl' => $TCA['tx_advertiser_dispatch']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_dispatch']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dispatch.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dispatch.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_price_options'] = array(
	'ctrl' => $TCA['tx_advertiser_price_options']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol',
	),
	'feInterface' => $TCA['tx_advertiser_price_options']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_price_options.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_price_options.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => '',
		),
	),
);



$TCA['tx_advertiser_vat'] = array(
	'ctrl' => $TCA['tx_advertiser_vat']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol,rate,country',
	),
	'feInterface' => $TCA['tx_advertiser_vat']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0',
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range' => array(
					'upper' => mktime(3,
					14,
					7,
					1,
					19,
					2038),
					'lower' => mktime(0,
					0,
					0,
					date('m') - 1,
					date('d'),
					date('Y')),
				),
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_vat.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'title_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_vat.title_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'rate' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_vat.rate',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2',
			),
		),
		'country' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_vat.country',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table'       => 'static_countries',
				'foreign_table_where' => 'AND static_countries.pid=###SITEROOT### ORDER BY static_countries.uid',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
				"MM"                  => "tx_advertiser_vat_country_mm",
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_lang_ol;;;;3-3-3, rate, country',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => 'starttime, endtime',
		),
	),
);



$TCA['tx_advertiser_classes'] = array(
	'ctrl' => $TCA['tx_advertiser_classes']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,title,fe_group_access,fe_group_accesstype,dwelltime,trialperiod,price,credit_consumption,credittype,vatincluded,vatrate,template_select,templatefile,infotext,infotext_lang_ol,storage_pid,pages',
	),
	'feInterface' => $TCA['tx_advertiser_classes']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array(
						'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', - 1,
					),
					array(
						'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
						0,
					),
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table' => 'tx_advertiser_classes',
				'foreign_table_where' => 'AND tx_advertiser_classes.pid=###CURRENT_PID### AND tx_advertiser_classes.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0',
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range' => array(
					'upper' => mktime(3,
					14,
					7,
					1,
					19,
					2038),
					'lower' => mktime(0,
					0,
					0,
					date('m') - 1,
					date('d'),
					date('Y')),
				),
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'fe_group_access' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_access',
			'config' => array(
				'type'                => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'fe_groups',
##				'foreign_table_where' => 'AND fe_groups.pid=###STORAGE_PID### ORDER BY fe_groups.uid',
				'foreign_table_where' => 'ORDER BY fe_groups.title',
				'size'                => 1,
				'minitems'            => 1,
				'maxitems'            => 1,
			),
		),
		'fe_group_accesstype' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_accesstype',
			'config' => array(
				'type' => 'radio',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_accesstype.I.0',
						'listonly',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_accesstype.I.1',
						'bid',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_accesstype.I.2',
						'request',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.fe_group_accesstype.I.3',
						'bidandrequest',
					),
				),
				'default' => 'bidandrequest',
			),
		),
		'dwelltime' => array(
			'displayCond' => 'FIELD:fe_group_accesstype:!=:listonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.dwelltime',
			'config' => array(
				'type' => 'text',
				'cols' => 8,
				'rows' => 5,
			),
		),
		'trialperiod' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.trialperiod',
			'config' => array (
				'type' => 'input',
				'size' => '4',
				'eval' => 'int,nospace',
			)
		),
		'price' => array(
			'displayCond' => 'FIELD:fe_group_accesstype:!=:listonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.price',
			'config' => array(
				'type' => 'text',
				'cols' => 8,
				'rows' => 5,
				'eval' => 'tx_advertiser_evalfield_price',
			),
		),
		'credit_consumption' => array(
			'displayCond'   => 'FIELD:fe_group_accesstype:!=:listonly',
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.credit_consumption',
			'config' => array(
				'type' => 'text',
				'cols' => 8,
				'rows' => 5,
			),
		),
/*
		'credittype' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.credittype',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.credittype.I.0',
						0,
					),
				),
				'foreign_table'       => 'tx_advertiser_credittypes',
				'foreign_table_where' => 'AND tx_advertiser_credittypes.pid=###STORAGE_PID### AND tx_advertiser_credittypes.deleted = 0 AND tx_advertiser_credittypes.hidden = 0 ORDER BY tx_advertiser_credittypes.sorting',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
			),
			'displayCond' => 'FIELD:fe_group_accesstype:!=:listonly',
		),
*/
		'vatincluded' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.vatincluded',
			'config' => array(
				'type' => 'radio',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.vatincluded.I.0',
						'excluded',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.vatincluded.I.1',
						'included',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.vatincluded.I.2',
						'zerovat',
					),
				),
			),
		),
		'vatrate' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.vatrate',
			'config' => array(
				'type' => 'input',
				'size' => '6',
				'eval' => 'double2,nospace',
				'default' => $defaultVatRate,
			),
			'displayCond' => 'FIELD:vatincluded:!=:zerovat',
		),
		'template_select' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.template_select',
			'config' => array(
				'type' => 'radio',
				'items' => array(
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.template_select.I.0',
						'standard',
					),
					array(
						'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.template_select.I.1',
						'file',
					),
				),
				'default' => 'standard',
			),
			'displayCond' => 'FIELD:fe_group_accesstype:!=:listonly',
		),
		'templatefile' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.templatefile',
			'config' => array(
				'type' => 'flex',
				'ds' => array (
					'default' => 'FILE:EXT:advertiser/pi3/flexform_tx_advertiser_classes_templatefile.xml',
				),

##				'type'          => 'group',
##				'internal_type' => 'file',
##				'allowed'       => '',
##				'disallowed'    => 'php,php3',
##				'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
##				'uploadfolder'  => 'uploads/tx_advertiser',
##				'size'          => 1,
##				'minitems'      => 0,
##				'maxitems'      => 1,
			),
			'displayCond'   => 'FIELD:template_select:=:file',
		),
		'infotext' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.infotext',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
			'displayCond'   => 'FIELD:fe_group_accesstype:=:listonly',
		),
		'infotext_lang_ol' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.infotext_lang_ol',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
			'displayCond'   => 'FIELD:fe_group_accesstype:=:listonly',
		),
		'storage_pid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.storage_pid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'show_thumbs' => 1,
				'size' => '1',
				'maxitems' => 1,
			),
			'displayCond'   => 'FIELD:fe_group_accesstype:!=:listonly',
		),
		'pages' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_classes.pages',
			'config' => array (
				'type'          => 'group',
				'internal_type' => 'db',
				'allowed'       => 'pages',
				'size'          => 5,
				'minitems'      => 0,
				'maxitems'      => 5,
				'show_thumbs'   => 1,
			),
		),
	),

	'types' => array(
		'0' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, fe_group_access;;2;;1-1-1, vatincluded;;3, template_select;;4;;1-1-1, infotext;;5, storage_pid;;;;1-1-1, pages;;;;1-1-1',
		),
	),

	'palettes' => array(
		'1' => array(
			'showitem' => 'starttime, endtime',
		),
		'2' => array(
##			'showitem' => 'fe_group_accesstype, dwelltime, price, credit_consumption, credittype',
			'showitem' => 'fe_group_accesstype, trialperiod, dwelltime, price, credit_consumption',
		),
		'3' => array(
			'showitem' => 'vatrate',
		),
		'4' => array(
			'showitem' => 'templatefile',
		),
		'5' => array(
			'showitem' => 'infotext_lang_ol',
		),
	),
);



/* ToDo: delete
$TCA['tx_advertiser_dwelltime'] = array(
	'ctrl' => $TCA['tx_advertiser_dwelltime']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,dwelltime,contingent_consumption',
	),
	'feInterface' => $TCA['tx_advertiser_dwelltime']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array(
						'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', - 1,
					),
					array(
						'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
						0,
					),
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'',
						0,
					),
				),
				'foreign_table' => 'tx_advertiser_dwelltime',
				'foreign_table_where' => 'AND tx_advertiser_dwelltime.pid=###CURRENT_PID### AND tx_advertiser_dwelltime.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0',
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range' => array(
					'upper' => mktime(3,
					14,
					7,
					1,
					19,
					2038),
					'lower' => mktime(0,
					0,
					0,
					date('m') - 1,
					date('d'),
					date('Y')),
				),
			),
		),
		'dwelltime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dwelltime.dwelltime',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'range' => array(
					'lower' => 0,
					'upper' => 1000,
				),
				'eval' => 'required,int,nospace',
			),
		),
		'contingent_consumption' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_dwelltime.contingent_consumption',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'range' => array(
					'lower' => 0,
					'upper' => 1000,
				),
				'eval' => 'required,int,nospace',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, dwelltime, contingent_consumption',
		),
	),
	'palettes' => array(
		'1' => array(
			'showitem' => 'starttime, endtime',
		),
	),
);
*/



$TCA['tx_advertiser_credittypes'] = array (
	'ctrl' => $TCA['tx_advertiser_credittypes']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,title'
	),
	'feInterface' => $TCA['tx_advertiser_credittypes']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_credittypes.title',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,nospace',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_advertiser_sessioncache'] = array (
	'ctrl' => $TCA['tx_advertiser_sessioncache']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'ad,sessioncontent'
	),
	'feInterface' => $TCA['tx_advertiser_sessioncache']['feInterface'],
	'columns' => array (
		'ad' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_sessioncache.ad',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_advertiser_ads',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'sessioncontent' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:advertiser/locallang_db.xml:tx_advertiser_sessioncache.sessioncontent',
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'ad;;;;1-1-1, sessioncontent')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>