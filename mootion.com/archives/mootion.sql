-- Sample mOOtion.com database creation script

SET AUTOCOMMIT=0;
START TRANSACTION;

-- 
-- Database: `mootiondb`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `blogs`
-- 

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE IF NOT EXISTS `blogs` (
  `blog_id` int(20) NOT NULL,
  `blog_key` varchar(35) default NULL,
  `blog_type` enum('normal','blog') NOT NULL default 'normal',
  `blog_rss` varchar(64) NOT NULL default '',
  `blog_rss2` varchar(64) NOT NULL default '',
  `blog_atom` varchar(64) NOT NULL default '',
  `blog_url` varchar(64) default NULL,
  PRIMARY KEY  (`blog_id`),
  UNIQUE KEY `key` (`blog_key`)
) TYPE=MyISAM AUTO_INCREMENT=15 ;


-- 
-- Table structure for table `categories`
-- 

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category__auto_id` int(11) NOT NULL,
  `category_lang` varchar(4) NOT NULL default 'es',
  `category_id` int(11) NOT NULL default '0',
  `category_parent` int(11) NOT NULL default '0',
  `category_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`category__auto_id`),
  UNIQUE KEY `category_lang` (`category_lang`,`category_id`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

-- 
-- Table structure for table `comments`
-- 

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(20) NOT NULL,
  `comment_randkey` int(11) NOT NULL default '0',
  `comment_parent` int(20) default '0',
  `comment_link_id` int(20) NOT NULL default '0',
  `comment_user_id` int(20) NOT NULL default '0',
  `comment_date` timestamp NOT NULL,
  `comment_karma` smallint(6) NOT NULL default '0',
  `comment_nick` varchar(32) default NULL,
  `comment_content` text NOT NULL,
  PRIMARY KEY  (`comment_id`),
  UNIQUE KEY `comments_randkey` (`comment_randkey`,`comment_link_id`,`comment_user_id`),
  KEY `comment_link_id_2` (`comment_link_id`,`comment_date`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

-- 
-- Table structure for table `friends`
-- 

DROP TABLE IF EXISTS `friends`;
CREATE TABLE IF NOT EXISTS `friends` (
  `friend_type` enum('affiliate','manual','hide') NOT NULL default 'affiliate',
  `friend_from` int(10) NOT NULL default '0',
  `friend_to` int(10) NOT NULL default '0',
  `friend_value` decimal(10,6) NOT NULL default '0.000000',
  UNIQUE KEY `friend_type_2` (`friend_type`,`friend_from`,`friend_to`,`friend_value`)
) TYPE=MyISAM;

-- 
-- Table structure for table `languages`
-- 

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `language_id` int(11) NOT NULL,
  `language_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_id`),
  UNIQUE KEY `language_name` (`language_name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Table structure for table `links`
-- 

DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `link_id` int(20) NOT NULL,
  `link_author` int(20) NOT NULL default '0',
  `link_blog` int(20) default '0',
  `link_status` enum('discard','queued','published','abuse','duplicated') NOT NULL default 'discard',
  `link_randkey` int(20) NOT NULL default '0',
  `link_votes` int(20) NOT NULL default '0',
  `link_karma` decimal(10,2) NOT NULL default '0.00',
  `link_modified` timestamp NOT NULL,
  `link_date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `link_published_date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `link_category` int(11) NOT NULL default '0',
  `link_lang` varchar(4) NOT NULL default 'es',
  `link_url` varchar(200) NOT NULL default '',
  `link_url_title` text,
  `link_title` text NOT NULL,
  `link_content` text NOT NULL,
  `link_tags` text,
  `link_viaurl` varchar(200) default NULL,
  `link_size` int(11) default NULL,
  `link_embedhtml` text,
  PRIMARY KEY  (`link_id`),
  KEY `link_author` (`link_author`),
  KEY `link_url` (`link_url`),
  KEY `link_date` (`link_date`),
  KEY `link_published_date` (`link_published_date`),
  FULLTEXT KEY `link_url_2` (`link_url`,`link_url_title`,`link_title`,`link_content`,`link_tags`),
  FULLTEXT KEY `link_tags` (`link_tags`)
) TYPE=MyISAM AUTO_INCREMENT=299 ;

-- 
-- Table structure for table `tags`
-- 

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `tag_link_id` int(11) NOT NULL default '0',
  `tag_lang` varchar(4) NOT NULL default 'es',
  `tag_date` timestamp NOT NULL,
  `tag_words` varchar(64) NOT NULL default '',
  UNIQUE KEY `tag_link_id` (`tag_link_id`,`tag_lang`,`tag_words`),
  KEY `tag_lang` (`tag_lang`,`tag_date`)
) TYPE=MyISAM;

-- 
-- Table structure for table `trackbacks`
-- 

DROP TABLE IF EXISTS `trackbacks`;
CREATE TABLE IF NOT EXISTS `trackbacks` (
  `trackback_id` int(10) unsigned NOT NULL,
  `trackback_link_id` int(11) NOT NULL default '0',
  `trackback_user_id` int(11) NOT NULL default '0',
  `trackback_type` enum('in','out') NOT NULL default 'in',
  `trackback_status` enum('ok','pendent','error') NOT NULL default 'pendent',
  `trackback_modified` timestamp NOT NULL,
  `trackback_date` timestamp NULL default NULL,
  `trackback_url` varchar(200) NOT NULL default '',
  `trackback_title` text,
  `trackback_content` text,
  PRIMARY KEY  (`trackback_id`),
  UNIQUE KEY `trackback_link_id_2` (`trackback_link_id`,`trackback_type`,`trackback_url`),
  KEY `trackback_link_id` (`trackback_link_id`),
  KEY `trackback_url` (`trackback_url`),
  KEY `trackback_date` (`trackback_date`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;


-- 
-- Table structure for table `users`
-- 
-- Creation: Feb 07, 2006 at 06:40 AM
-- Last update: May 14, 2006 at 06:23 AM
-- Last check: May 14, 2006 at 07:56 PM
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(20) NOT NULL,
  `user_login` varchar(32) NOT NULL default '',
  `user_level` enum('normal','special','blogger','admin','god') NOT NULL default 'normal',
  `user_modification` timestamp NOT NULL,
  `user_date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `user_validated_date` timestamp NULL default NULL,
  `user_ip` varchar(32) default NULL,
  `user_pass` varchar(64) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_names` varchar(128) NOT NULL default '',
  `user_lang` int(11) NOT NULL default '1',
  `user_karma` decimal(10,2) default '6.00',
  `user_url` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  KEY `user_email` (`user_email`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- 
-- Table structure for table `votes`
-- 

DROP TABLE IF EXISTS `votes`;
CREATE TABLE IF NOT EXISTS `votes` (
  `vote_id` int(20) NOT NULL,
  `vote_type` enum('links','comments') NOT NULL default 'links',
  `vote_date` timestamp NOT NULL,
  `vote_link_id` int(20) NOT NULL default '0',
  `vote_user_id` int(20) NOT NULL default '0',
  `vote_value` smallint(11) NOT NULL default '1',
  `vote_ip` char(24) default NULL,
  PRIMARY KEY  (`vote_id`),
  KEY `user_id` (`vote_user_id`),
  KEY `vote_type` (`vote_type`,`vote_link_id`,`vote_user_id`,`vote_ip`),
  KEY `vote_type_2` (`vote_type`,`vote_user_id`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=860 ;

COMMIT;
