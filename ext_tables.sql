#
# Table structure for table 'tx_advertiser_ads_payment_mm'
#
#
CREATE TABLE tx_advertiser_ads_payment_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_advertiser_ads'
#
CREATE TABLE tx_advertiser_ads (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	product_group int(11) DEFAULT '0' NOT NULL,
	manufacturer int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	description text,
	category int(11) DEFAULT '0' NOT NULL,
	state int(11) DEFAULT '0' NOT NULL,
	dateofproduction int(11) DEFAULT '0' NOT NULL,
	warranty int(11) DEFAULT '0' NOT NULL,
	image text,
	price double(11,2) DEFAULT '0.00' NOT NULL,
	vat int(11) DEFAULT '0' NOT NULL,
	price_option int(11) DEFAULT '0' NOT NULL,
	shipping double(11,2) DEFAULT '0.00' NOT NULL,
	dispatch int(11) DEFAULT '0' NOT NULL,
	payment int(11) DEFAULT '0' NOT NULL,
	fe_user int(11) DEFAULT '0' NOT NULL,
	premium int(11) DEFAULT '0' NOT NULL,
	sold tinyint(3) DEFAULT '0' NOT NULL,
	productlink tinytext,
	remaining_term int(11) DEFAULT '0' NOT NULL,
	class text,
	ispreview tinyint(3) DEFAULT '0' NOT NULL,


	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_manufacturers'
#
CREATE TABLE tx_advertiser_manufacturers (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,
	image text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_states'
#
CREATE TABLE tx_advertiser_states (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_productgroups'
#
CREATE TABLE tx_advertiser_productgroups (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_categories'
#
CREATE TABLE tx_advertiser_categories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_payment'
#
CREATE TABLE tx_advertiser_payment (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_dispatch'
#
CREATE TABLE tx_advertiser_dispatch (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_price_options'
#
CREATE TABLE tx_advertiser_price_options (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);




#
# Table structure for table 'tx_advertiser_vat_country_mm'
#
#
CREATE TABLE tx_advertiser_vat_country_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_advertiser_vat'
#
CREATE TABLE tx_advertiser_vat (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	title_lang_ol tinytext,
	rate double(11,2) DEFAULT '0.00' NOT NULL,
	country int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_classes'
#
CREATE TABLE tx_advertiser_classes (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	fe_group_access int(11) DEFAULT '0' NOT NULL,
	fe_group_accesstype varchar(16) DEFAULT '' NOT NULL,
	dwelltime text,
	trialperiod int(11) DEFAULT '0' NOT NULL,
	price text,
	credit_consumption text,
	credittype int(11) DEFAULT '0' NOT NULL,
	vatincluded varchar(16) DEFAULT '' NOT NULL,
	vatrate double(11,2) DEFAULT '0.00' NOT NULL,
	template_select varchar(8) DEFAULT '' NOT NULL,
	templatefile mediumtext,
	infotext tinytext,
	infotext_lang_ol tinytext,
	storage_pid int(11) DEFAULT '0' NOT NULL,
	pages tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_advertiser_sessioncache'
#
CREATE TABLE tx_advertiser_sessioncache (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	ad text,
	sessioncontent text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



##
## Table structure for table 'tx_advertiser_dwelltime'
##
#CREATE TABLE tx_advertiser_dwelltime (
#	uid int(11) NOT NULL auto_increment,
#	pid int(11) DEFAULT '0' NOT NULL,
#	tstamp int(11) DEFAULT '0' NOT NULL,
#	crdate int(11) DEFAULT '0' NOT NULL,
#	cruser_id int(11) DEFAULT '0' NOT NULL,
#	sys_language_uid int(11) DEFAULT '0' NOT NULL,
#	l10n_parent int(11) DEFAULT '0' NOT NULL,
#	l10n_diffsource mediumtext,
#	sorting int(10) DEFAULT '0' NOT NULL,
#	deleted tinyint(4) DEFAULT '0' NOT NULL,
#	hidden tinyint(4) DEFAULT '0' NOT NULL,
#	starttime int(11) DEFAULT '0' NOT NULL,
#	endtime int(11) DEFAULT '0' NOT NULL,
#
#	PRIMARY KEY (uid),
#	KEY parent (pid)
#);



##
## Table structure for table 'tx_advertiser_credittypes'
##
#CREATE TABLE tx_advertiser_credittypes (
#	uid int(11) NOT NULL auto_increment,
#	pid int(11) DEFAULT '0' NOT NULL,
#	tstamp int(11) DEFAULT '0' NOT NULL,
#	crdate int(11) DEFAULT '0' NOT NULL,
#	cruser_id int(11) DEFAULT '0' NOT NULL,
#	sorting int(10) DEFAULT '0' NOT NULL,
#	deleted tinyint(4) DEFAULT '0' NOT NULL,
#	hidden tinyint(4) DEFAULT '0' NOT NULL,
#	title tinytext,
#
#	PRIMARY KEY (uid),
#	KEY parent (pid)
#);



#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_advertiser_accept_gtc tinyint(3) DEFAULT '0' NOT NULL,
	tx_advertiser_merchant tinyint(3) DEFAULT '0' NOT NULL,
	tx_advertiser_vat_id tinytext
);



#
# Table structure for table 'fe_groups'
#
CREATE TABLE fe_groups (
    title_lang_ol tinytext
);


#
# Table structure for table 'static_country_zones'
#
CREATE TABLE static_country_zones (
	zn_name_local_lang_ol tinytext
);