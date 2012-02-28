/*
SQLyog Enterprise - MySQL GUI v6.13
MySQL - 5.0.54-log : Database - deepaksareen
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `pages` */

CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `parent_id` bigint(20) unsigned default NULL,
  `slug` varchar(255) default NULL,
  `nleft` bigint(20) unsigned NOT NULL,
  `nright` bigint(20) unsigned NOT NULL,
  `nlevel` bigint(20) unsigned NOT NULL,
  `active` tinyint(1) unsigned default NULL,
  `navigation` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `pages_i18n` */

CREATE TABLE `pages_i18n` (
  `i18n_foreign_key` bigint(20) unsigned NOT NULL,
  `i18n_locale` char(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) default NULL,
  `metatitle` varchar(255) default NULL,
  `description` text,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`i18n_foreign_key`,`i18n_locale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;