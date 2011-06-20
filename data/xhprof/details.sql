/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Version : 50502
 Source Host           : localhost
 Source Database       : xhprof

 Target Server Version : 50502
 File Encoding         : utf-8

 Date: 06/13/2011 02:22:06 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `details`
-- ----------------------------
DROP TABLE IF EXISTS `details`;
CREATE TABLE `details` (
  `id` char(17) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `c_url` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `server name` varchar(64) DEFAULT NULL,
  `perfdata` mediumblob,
  `type` tinyint(4) DEFAULT NULL,
  `cookie` blob,
  `post` blob,
  `get` blob,
  `pmu` int(11) DEFAULT NULL,
  `wt` int(11) DEFAULT NULL,
  `cpu` int(11) DEFAULT NULL,
  `server_id` char(3) NOT NULL DEFAULT 't11',
  `aggregateCalls_include` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `c_url` (`c_url`),
  KEY `cpu` (`cpu`),
  KEY `wt` (`wt`),
  KEY `pmu` (`pmu`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

