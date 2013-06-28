/*
SQLyog Community Edition- MySQL GUI v8.2 RC2
MySQL - 5.0.84-log : Database - fwurks2
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
  `id` int(10) unsigned NOT NULL auto_increment,
  `permissions` text NOT NULL,
  `active` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `admin_groups` */

insert  into `admin_groups`(`id`,`permissions`,`active`) values (1,'all',1);
insert  into `admin_groups`(`id`,`permissions`,`active`) values (2,'dashboard{}|projects{index,add,edit}|project_objects{index,add,edit}|admin_users{profile}',1);

/*Table structure for table `admin_groups_admin_users` */

DROP TABLE IF EXISTS `admin_groups_admin_users`;

CREATE TABLE `admin_groups_admin_users` (
  `admin_group_id` int(3) unsigned NOT NULL,
  `admin_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`admin_group_id`,`admin_user_id`),
  KEY `user` (`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `admin_groups_admin_users` */

insert  into `admin_groups_admin_users`(`admin_group_id`,`admin_user_id`) values (1,1);

/*Table structure for table `admin_groups_i18n` */

DROP TABLE IF EXISTS `admin_groups_i18n`;

CREATE TABLE `admin_groups_i18n` (
  `i18n_foreign_key` int(10) unsigned NOT NULL,
  `i18n_locale` varchar(5) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY  (`i18n_foreign_key`,`i18n_locale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `admin_groups_i18n` */

insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (1,'en_EN','Administrator');
insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (1,'bg_BG','Администратор');
insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (2,'en_EN','Moderator');
insert  into `admin_groups_i18n`(`i18n_foreign_key`,`i18n_locale`,`title`) values (2,'bg_BG','Модератор');

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
insert  into `admin_settings`(`name`,`value`,`comment`) values ('records_per_page','20','listing: how many record to list in one page');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('edit_link_titles','1','listing: whether titles are linking to the edit page for the current record or no');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('active_by_default','1','add/edit');
insert  into `admin_settings`(`name`,`value`,`comment`) values ('google_analytics','','Google Analytics ID');

/*Table structure for table `admin_users` */

DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `password` char(60) NOT NULL,
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `nick_name` varchar(100) default NULL,
  `display_name` tinyint(2) unsigned default NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `admin_users` */

insert  into `admin_users`(`id`,`username`,`password`,`first_name`,`last_name`,`nick_name`,`display_name`,`email`,`active`) values (1,'admin','$2y$11$s86jtJtVpnmnGDwAwMCkFeO9mY9M.4R51/Bc7zGkpcfg4a0Vo/eF.','Admin','Adminov','admin',2,'admin@avalith.bg',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
