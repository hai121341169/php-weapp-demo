/*
Navicat MySQL Data Transfer

Source Server         : 192.168.33.10
Source Server Version : 50636
Source Host           : 192.168.33.10:3306
Source Database       : hucai_cloud

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2017-07-19 20:16:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hc_image
-- ----------------------------
DROP TABLE IF EXISTS `hc_image`;
CREATE TABLE `hc_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片id',
  `image_url` varchar(255) NOT NULL COMMENT '图片地址',
  `width` float NOT NULL DEFAULT '0' COMMENT '图片宽',
  `height` float NOT NULL DEFAULT '0' COMMENT '图片高',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `source` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '照片来源(1=小程序；2=公众号；)',
  `quality` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1=低质量；0=正常；1=高质量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hc_order_work
-- ----------------------------
DROP TABLE IF EXISTS `hc_order_work`;
CREATE TABLE `hc_order_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '作品信息id',
  `order_sn` varchar(32) NOT NULL,
  `work_id` varchar(10) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=处理中；1=待接单；2=已接单；3=处理结束',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=未删除；1=已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hc_order_work_image
-- ----------------------------
DROP TABLE IF EXISTS `hc_order_work_image`;
CREATE TABLE `hc_order_work_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_work_id` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=临时区;1=待提交区;2=已提交',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hc_order_work_image_sort
-- ----------------------------
DROP TABLE IF EXISTS `hc_order_work_image_sort`;
CREATE TABLE `hc_order_work_image_sort` (
  `order_work_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'hc_order_work表中的id',
  `sort` text COMMENT '排序用,隔开',
  PRIMARY KEY (`order_work_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hc_order_work_join
-- ----------------------------
DROP TABLE IF EXISTS `hc_order_work_join`;
CREATE TABLE `hc_order_work_join` (
  `order_work_id` int(11) NOT NULL COMMENT '订单作品id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  UNIQUE KEY `order_work_id` (`order_work_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hc_user
-- ----------------------------
DROP TABLE IF EXISTS `hc_user`;
CREATE TABLE `hc_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户Id',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `nickname` varchar(255) DEFAULT NULL,
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=未知;1=男;2=女',
  `openid` varchar(32) DEFAULT NULL COMMENT '用户openid',
  `unionid` varchar(32) DEFAULT NULL COMMENT '微信unionid',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT '用户图片',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
