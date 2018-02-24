/*
Navicat MySQL Data Transfer

Source Server         : 本地数据库
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : sspanel

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-10-24 19:53:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yft_order_info
-- ----------------------------
DROP TABLE IF EXISTS `yft_order_info`;
CREATE TABLE `yft_order_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ss_order` varchar(50) DEFAULT NULL,
  `yft_order` varchar(50) DEFAULT NULL,
  `price` varchar(10) DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL COMMENT '0代表未支付，1代表已支付',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yft_order_info
-- ----------------------------
