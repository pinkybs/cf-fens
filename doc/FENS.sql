/*
SQLyog Community Edition- MySQL GUI v6.13
MySQL - 5.0.67-log : Database - fens
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `fens`;

USE `fens`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `admin_user` */

DROP TABLE IF EXISTS `admin_user`;

CREATE TABLE `admin_user` (
  `uid` int(11) NOT NULL auto_increment,
  `login_id` char(40) default NULL,
  `password` char(40) default NULL,
  `name` varchar(255) default NULL,
  `status` tinyint(1) default '1' COMMENT '1-normal 0-abnormal',
  `create_time` datetime default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `admin_user` */

insert  into `admin_user`(`uid`,`login_id`,`password`,`name`,`status`,`create_time`) values (1,'administrator','a9c7180f3edda41a4117c44e2b338b1965c576b5','administrator',1,'2009-03-20 00:00:00');

/*Table structure for table `crawler_setting` */

DROP TABLE IF EXISTS `crawler_setting`;

CREATE TABLE `crawler_setting` (
  `number` tinyint(4) unsigned NOT NULL default '1' COMMENT 'enum:1,2,3,4,5',
  `timing` tinyint(4) unsigned NOT NULL default '24' COMMENT 'enum:1,5,10,15,24',
  `new_timing` tinyint(4) default '10' COMMENT 'enum:5,10,20,30',
  `last_new_crawling_time` int(10) unsigned NOT NULL default '0',
  `last_crawling_time` int(10) unsigned NOT NULL default '0',
  `depth` tinyint(4) unsigned NOT NULL default '10',
  `db_synchronize_timing` tinyint(4) unsigned NOT NULL default '24' COMMENT 'enum:1,5,10,15,24',
  `last_synchronize_time` int(10) unsigned default '0',
  `synchronize_running` tinyint(1) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `crawler_setting` */

insert  into `crawler_setting`(`number`,`timing`,`new_timing`,`last_new_crawling_time`,`last_crawling_time`,`depth`,`db_synchronize_timing`,`last_synchronize_time`,`synchronize_running`) values (2,5,30,0,0,10,24,0,0);

/*Table structure for table `db_synchronize_log` */

DROP TABLE IF EXISTS `db_synchronize_log`;

CREATE TABLE `db_synchronize_log` (
  `id` int(11) NOT NULL auto_increment,
  `mode` tinyint(1) default '0' COMMENT '0-auto 1-manual',
  `begin_time` int(10) unsigned default '0',
  `end_time` int(10) unsigned default '0',
  `cur_percent` tinyint(4) default '0' COMMENT '1~100 ',
  `is_ended` tinyint(1) default '0' COMMENT '1-ended',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `db_synchronize_log` */

/*Table structure for table `nb_site_area` */

DROP TABLE IF EXISTS `nb_site_area`;

CREATE TABLE `nb_site_area` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `sort` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `nb_site_area` */

insert  into `nb_site_area`(`id`,`name`,`sort`) values (1,'北海道',1),(2,'青森県',2),(3,'岩手県',3),(4,'宮城県',4),(5,'秋田県',5),(6,'山形県',6),(7,'福島県',7),(8,'茨城県',8),(9,'栃木県',9),(10,'群馬県',10),(11,'埼玉県',11),(12,'千葉県',12),(13,'東京都',13),(14,'神奈川県',14),(15,'新潟県',15),(16,'富山県',16),(17,'石川県',17),(18,'福井県',18),(19,'山梨県',19),(20,'長野県',20),(21,'岐阜県',21),(22,'静岡県',22),(23,'愛知県',23),(24,'三重県',24),(25,'滋賀県',25),(26,'京都府',26),(27,'大阪府',27),(28,'兵庫県',28),(29,'奈良県',29),(30,'和歌山県',30),(31,'鳥取県',31),(32,'島根県',32),(33,'岡山県',33),(34,'広島県',34),(35,'山口県',35),(36,'徳島県',36),(37,'香川県',37),(38,'愛媛県',38),(39,'高知県',39),(40,'福岡県',40),(41,'佐賀県',41),(42,'長崎県',42),(43,'熊本県',43),(44,'大分県',44),(45,'宮崎県',45),(46,'鹿児島県',46),(47,'沖縄県',47),(48,'その他',48);

/*Table structure for table `nb_site_category` */

DROP TABLE IF EXISTS `nb_site_category`;

CREATE TABLE `nb_site_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `sort` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `nb_site_category` */

/*Table structure for table `search_keyword_daily_stat` */

DROP TABLE IF EXISTS `search_keyword_daily_stat`;

CREATE TABLE `search_keyword_daily_stat` (
  `search_date` date NOT NULL,
  `search_keyword` varchar(255) NOT NULL,
  `search_count` int(11) default NULL,
  `create_time` datetime default NULL,
  PRIMARY KEY  (`search_date`,`search_keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `search_keyword_daily_stat` */

/*Table structure for table `search_keyword_log` */

DROP TABLE IF EXISTS `search_keyword_log`;

CREATE TABLE `search_keyword_log` (
  `id` bigint(20) NOT NULL auto_increment,
  `keyword` varchar(255) default NULL,
  `ip_address` varchar(100) default NULL,
  `browser` varchar(100) default NULL,
  `create_time` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `search_keyword_log` */

/*Table structure for table `site` */

DROP TABLE IF EXISTS `site`;

CREATE TABLE `site` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `url` varchar(255) NOT NULL,
  `toppage` varchar(255) NOT NULL,
  `area_id` int(11) NOT NULL default '0' COMMENT 'reference nb_site_area.id',
  `category_id` int(11) NOT NULL default '0' COMMENT 'reference nb_site_category.id',
  `page_count` int(11) NOT NULL default '0',
  `last_crawling_time` datetime default NULL,
  `status` tinyint(4) NOT NULL default '0' COMMENT '0-uncrawl 1-crawling 2-crawled',
  `create_time` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `site` */

/*Table structure for table `site_page` */

DROP TABLE IF EXISTS `site_page`;

CREATE TABLE `site_page` (
  `id` bigint(20) NOT NULL auto_increment,
  `site_id` int(11) default NULL COMMENT 'reference crawl_site.id',
  `url` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `body` text,
  `summary` text,
  `depth` tinyint(4) default '-1',
  `create_time` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_url` (`url`),
  KEY `site_id` (`site_id`),
  FULLTEXT KEY `title` USING MECAB, NO NORMALIZE, 512 (`title`,`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `site_page` */

/*Table structure for table `site_page_fixed_rank` */

DROP TABLE IF EXISTS `site_page_fixed_rank`;

CREATE TABLE `site_page_fixed_rank` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(255) default NULL,
  `page_id` int(11) default NULL COMMENT 'reference site_page.id',
  `sort` int(11) default NULL,
  `create_time` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `site_page_fixed_rank` */

/* Procedure structure for procedure `pd_get_crawl_site_pgcount_pglasttime` */

/*!50003 DROP PROCEDURE IF EXISTS  `pd_get_crawl_site_pgcount_pglasttime` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mysql`@`%` PROCEDURE `pd_get_crawl_site_pgcount_pglasttime`()
BEGIN
update site as a set page_count=(
select b.page_count from (SELECT count(id) as page_count,site_id FROM site_page group by site_id) as b where a.id=b.site_id
);
update site as a set last_crawling_time=(
select b.last_crawling_time from (SELECT MAX(FROM_UNIXTIME(create_time)) as last_crawling_time,site_id FROM site_page group by site_id) as b where a.id=b.site_id
);
    END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
