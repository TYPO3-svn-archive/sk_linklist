#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_sklinklist_view int(11) unsigned DEFAULT '0' NOT NULL
);



#
# Table structure for table 'tx_sklinklist_categories'
#
CREATE TABLE tx_sklinklist_categories (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	category tinytext NOT NULL,
	subcategory int(11) unsigned DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_sklinklist_links'
#
CREATE TABLE tx_sklinklist_links (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	url tinytext NOT NULL,
	description tinytext NOT NULL,
	label tinytext NOT NULL,
	category blob NOT NULL,
	rating tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);