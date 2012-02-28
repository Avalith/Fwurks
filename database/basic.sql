/*
SQLyog Community Edition- MySQL GUI v8.2 RC2
MySQL - 5.0.84-log : Database - fwurks
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `admin_groups` */

DROP TABLE IF EXISTS `admin_groups`;

CREATE TABLE `admin_groups` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `permissions` text NOT NULL,
  `active` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `admin_groups` */

insert  into `admin_groups`(`id`,`permissions`,`active`) values (1,'all',1);

/*Table structure for table `admin_groups_i18n` */

DROP TABLE IF EXISTS `admin_groups_i18n`;

CREATE TABLE `admin_groups_i18n` (
  `i18n_foreign_key` bigint(20) unsigned NOT NULL,
  `i18n_locale` varchar(5) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY  (`i18n_foreign_key`,`i18n_locale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `admin_groups_i18n` */

insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (1,'bg_BG','Администратор');
insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (1,'en_EN','Administrators');

/*Table structure for table `admin_settings` */

DROP TABLE IF EXISTS `admin_settings`;

CREATE TABLE `admin_settings` (
  `name` varchar(255) NOT NULL,
  `value` text,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `admin_settings` */

insert  into `admin_settings`(`name`,`value`,`comment`) values ('default_group','2','the id of the default group');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('records_per_page','50','listing: how many record to list in one page');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('edit_link_titles','1','listing: whether titles are linking to the edit page for the current record or no');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('google_analytics','','Google Analytics ID');

/*Table structure for table `admin_users` */

DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `admin_group_id` bigint(20) unsigned NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `nick_name` varchar(100) default NULL,
  `display_name` tinyint(2) unsigned default NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `admin_users` */

insert  into `admin_users`(`id`,`admin_group_id`,`username`,`password`,`first_name`,`last_name`,`nick_name`,`display_name`,`email`,`active`) values (1,1,'admin','21232f297a57a5a743894a0e4a801fc3','Admin','Adminov','diodi',2,'karamfil.pc@gmail.com',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
