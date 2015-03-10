/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50535
Source Host           : localhost:3306
Source Database       : qbit3

Target Server Type    : MYSQL
Target Server Version : 50535
File Encoding         : 65001

Date: 2014-10-21 14:50:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for catalogs
-- ----------------------------
DROP TABLE IF EXISTS `catalogs`;
CREATE TABLE `catalogs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `design_id` int(10) NOT NULL,
  `form_id` int(10) NOT NULL,
  `catalog_name` varchar(255) NOT NULL,
  `catalog_title` varchar(255) DEFAULT NULL,
  `catalog_content` longtext,
  `catalog_group` varchar(255) NOT NULL,
  `is_category` tinyint(1) NOT NULL DEFAULT '0',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '1',
  `insert_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of catalogs
-- ----------------------------

-- ----------------------------
-- Table structure for contents
-- ----------------------------
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `content_name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contents
-- ----------------------------

-- ----------------------------
-- Table structure for designs
-- ----------------------------
DROP TABLE IF EXISTS `designs`;
CREATE TABLE `designs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `design_name` varchar(255) NOT NULL,
  `block` text NOT NULL,
  `structure` text NOT NULL,
  `additional_style` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of designs
-- ----------------------------

-- ----------------------------
-- Table structure for forms
-- ----------------------------
DROP TABLE IF EXISTS `forms`;
CREATE TABLE `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `design_id` int(10) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of forms
-- ----------------------------

-- ----------------------------
-- Table structure for form_fields
-- ----------------------------
DROP TABLE IF EXISTS `form_fields`;
CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_title` varchar(255) NOT NULL,
  `field_width` smallint(6) NOT NULL DEFAULT '50',
  `field_type_id` smallint(2) NOT NULL,
  `field_select_id` int(11) DEFAULT NULL,
  `linked_field_id` int(11) NOT NULL,
  `translation` smallint(1) NOT NULL DEFAULT '0',
  `required` smallint(1) NOT NULL DEFAULT '0',
  `datetime` smallint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form_fields
-- ----------------------------

-- ----------------------------
-- Table structure for form_field_selects
-- ----------------------------
DROP TABLE IF EXISTS `form_field_selects`;
CREATE TABLE `form_field_selects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `select_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form_field_selects
-- ----------------------------

-- ----------------------------
-- Table structure for form_field_select_options
-- ----------------------------
DROP TABLE IF EXISTS `form_field_select_options`;
CREATE TABLE `form_field_select_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_select_id` int(11) NOT NULL,
  `option_title` varchar(255) NOT NULL,
  `option_value` varchar(255) NOT NULL,
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `form_field_selects_fk` (`field_select_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form_field_select_options
-- ----------------------------

-- ----------------------------
-- Table structure for form_field_types
-- ----------------------------
DROP TABLE IF EXISTS `form_field_types`;
CREATE TABLE `form_field_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form_field_types
-- ----------------------------
INSERT INTO `form_field_types` VALUES ('1', 'text');
INSERT INTO `form_field_types` VALUES ('2', 'textarea');
INSERT INTO `form_field_types` VALUES ('3', 'select');
INSERT INTO `form_field_types` VALUES ('4', 'button');
INSERT INTO `form_field_types` VALUES ('5', 'upload');
INSERT INTO `form_field_types` VALUES ('6', 'checkbox');
INSERT INTO `form_field_types` VALUES ('7', 'multiselect');

-- ----------------------------
-- Table structure for form_field_values
-- ----------------------------
DROP TABLE IF EXISTS `form_field_values`;
CREATE TABLE `form_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `row_id` int(11) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of form_field_values
-- ----------------------------

-- ----------------------------
-- Table structure for grants
-- ----------------------------
DROP TABLE IF EXISTS `grants`;
CREATE TABLE `grants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) NOT NULL,
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `grant_type` varchar(100) NOT NULL COMMENT 'CRUD',
  `resource_type` varchar(100) NOT NULL DEFAULT 'modules',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of grants
-- ----------------------------
INSERT INTO `grants` VALUES ('60', 'profiles', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('59', 'materials', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('58', 'forms', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('57', 'contents', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('56', 'catalogs', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('54', 'auth', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('53', 'auth', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('52', 'admin', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('51', 'admin', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('50', 'admin', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('49', 'grants', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('48', 'grants', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('47', 'grants', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('46', 'grants', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('45', 'cms', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('44', 'cms', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('43', 'cms', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('42', 'cms', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('41', 'auth', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('40', 'auth', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('39', 'crud', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('38', 'crud', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('37', 'crud', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('36', 'crud', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('35', 'pages', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('34', 'layouts', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('33', 'designs', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('32', 'admin', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('30', 'materials', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('29', 'materials', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('28', 'materials', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('27', 'profiles', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('26', 'profiles', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('25', 'profiles', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('24', 'forms', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('23', 'forms', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('22', 'forms', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('21', 'designs', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('20', 'designs', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('19', 'designs', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('18', 'catalogs', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('17', 'catalogs', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('16', 'catalogs', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('9', 'contents', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('8', 'contents', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('7', 'contents', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('6', 'pages', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('5', 'pages', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('4', 'pages', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('3', 'layouts', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('2', 'layouts', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('1', 'layouts', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('61', 'search', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('62', 'search', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('63', 'search', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('64', 'search', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('65', 'updates', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('66', 'licenses', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('67', 'licenses', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('68', 'licenses', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('69', 'licenses', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('70', 'updates', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('71', 'updates', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('72', 'updates', '0', 'DELETE', 'modules');
INSERT INTO `grants` VALUES ('73', 'translations', '0', 'GET', 'modules');
INSERT INTO `grants` VALUES ('74', 'translations', '0', 'PUT', 'modules');
INSERT INTO `grants` VALUES ('75', 'translations', '0', 'POST', 'modules');
INSERT INTO `grants` VALUES ('76', 'translations', '0', 'DELETE', 'modules');

-- ----------------------------
-- Table structure for languages
-- ----------------------------
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `short` varchar(10) NOT NULL,
  `language` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for layouts
-- ----------------------------
DROP TABLE IF EXISTS `layouts`;
CREATE TABLE `layouts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `design_id` int(10) NOT NULL,
  `layout_name` varchar(255) NOT NULL,
  `layout_content` longtext NOT NULL,
  `is_visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of layouts
-- ----------------------------

-- ----------------------------
-- Table structure for maps
-- ----------------------------
DROP TABLE IF EXISTS `maps`;
CREATE TABLE `maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lq` text NOT NULL,
  `container_type` varchar(255) NOT NULL,
  `container_id` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL,
  `design` varchar(255) NOT NULL,
  `material_design` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of maps
-- ----------------------------

-- ----------------------------
-- Table structure for materials
-- ----------------------------
DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `object_id` varchar(255) NOT NULL,
  `object_type` varchar(255) NOT NULL,
  `material_type` enum('image','doc','pdf') NOT NULL,
  `material_title` varchar(255) NOT NULL,
  `material_path` varchar(255) NOT NULL,
  `material_insert_date` datetime NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of materials
-- ----------------------------

-- ----------------------------
-- Table structure for pages
-- ----------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `layout_id` int(10) NOT NULL,
  `design_id` int(10) NOT NULL DEFAULT '0',
  `page_name` varchar(255) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_meta_title` text NOT NULL,
  `page_meta_keywords` text NOT NULL,
  `page_meta_description` text NOT NULL,
  `page_content` longtext NOT NULL,
  `page_menu_group` varchar(255) NOT NULL,
  `page_sub_menu` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_external_link`  varchar(1) NOT NULL DEFAULT '0',
  `external_url_target`  varchar(255) NOT NULL DEFAULT '',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pages
-- ----------------------------

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', 'Public');
INSERT INTO `roles` VALUES ('2', 'Administrator');

-- ----------------------------
-- Table structure for role_grants
-- ----------------------------
DROP TABLE IF EXISTS `role_grants`;
CREATE TABLE `role_grants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `grant_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_grant_fc1` (`role_id`),
  KEY `role_grant_fc2` (`grant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role_grants
-- ----------------------------
INSERT INTO `role_grants` VALUES ('1', '1', '1');
INSERT INTO `role_grants` VALUES ('4', '1', '4');
INSERT INTO `role_grants` VALUES ('5', '2', '5');
INSERT INTO `role_grants` VALUES ('6', '2', '6');
INSERT INTO `role_grants` VALUES ('7', '1', '7');
INSERT INTO `role_grants` VALUES ('17', '1', '16');
INSERT INTO `role_grants` VALUES ('20', '1', '19');
INSERT INTO `role_grants` VALUES ('23', '1', '22');
INSERT INTO `role_grants` VALUES ('26', '1', '25');
INSERT INTO `role_grants` VALUES ('31', '1', '28');
INSERT INTO `role_grants` VALUES ('35', '2', '32');
INSERT INTO `role_grants` VALUES ('39', '1', '36');
INSERT INTO `role_grants` VALUES ('43', '1', '40');
INSERT INTO `role_grants` VALUES ('45', '2', '42');
INSERT INTO `role_grants` VALUES ('46', '2', '43');
INSERT INTO `role_grants` VALUES ('47', '2', '44');
INSERT INTO `role_grants` VALUES ('48', '2', '45');
INSERT INTO `role_grants` VALUES ('49', '2', '46');
INSERT INTO `role_grants` VALUES ('50', '2', '47');
INSERT INTO `role_grants` VALUES ('51', '2', '48');
INSERT INTO `role_grants` VALUES ('52', '2', '49');
INSERT INTO `role_grants` VALUES ('53', '2', '50');
INSERT INTO `role_grants` VALUES ('54', '2', '51');
INSERT INTO `role_grants` VALUES ('55', '2', '52');
INSERT INTO `role_grants` VALUES ('58', '2', '1');
INSERT INTO `role_grants` VALUES ('59', '2', '4');
INSERT INTO `role_grants` VALUES ('60', '2', '7');
INSERT INTO `role_grants` VALUES ('61', '2', '16');
INSERT INTO `role_grants` VALUES ('62', '2', '33');
INSERT INTO `role_grants` VALUES ('63', '2', '34');
INSERT INTO `role_grants` VALUES ('64', '2', '35');
INSERT INTO `role_grants` VALUES ('65', '2', '37');
INSERT INTO `role_grants` VALUES ('66', '2', '41');
INSERT INTO `role_grants` VALUES ('67', '2', '56');
INSERT INTO `role_grants` VALUES ('68', '2', '57');
INSERT INTO `role_grants` VALUES ('69', '2', '58');
INSERT INTO `role_grants` VALUES ('70', '2', '59');
INSERT INTO `role_grants` VALUES ('71', '2', '60');
INSERT INTO `role_grants` VALUES ('72', '2', '2');
INSERT INTO `role_grants` VALUES ('73', '2', '3');
INSERT INTO `role_grants` VALUES ('74', '2', '8');
INSERT INTO `role_grants` VALUES ('75', '2', '9');
INSERT INTO `role_grants` VALUES ('76', '2', '17');
INSERT INTO `role_grants` VALUES ('77', '2', '18');
INSERT INTO `role_grants` VALUES ('78', '2', '19');
INSERT INTO `role_grants` VALUES ('79', '2', '20');
INSERT INTO `role_grants` VALUES ('80', '2', '21');
INSERT INTO `role_grants` VALUES ('81', '2', '22');
INSERT INTO `role_grants` VALUES ('82', '2', '23');
INSERT INTO `role_grants` VALUES ('83', '2', '24');
INSERT INTO `role_grants` VALUES ('84', '2', '25');
INSERT INTO `role_grants` VALUES ('85', '2', '26');
INSERT INTO `role_grants` VALUES ('86', '2', '27');
INSERT INTO `role_grants` VALUES ('87', '2', '28');
INSERT INTO `role_grants` VALUES ('88', '2', '29');
INSERT INTO `role_grants` VALUES ('89', '2', '30');
INSERT INTO `role_grants` VALUES ('90', '2', '36');
INSERT INTO `role_grants` VALUES ('91', '2', '38');
INSERT INTO `role_grants` VALUES ('92', '2', '39');
INSERT INTO `role_grants` VALUES ('93', '2', '40');
INSERT INTO `role_grants` VALUES ('94', '2', '53');
INSERT INTO `role_grants` VALUES ('95', '2', '54');
INSERT INTO `role_grants` VALUES ('96', '1', '37');
INSERT INTO `role_grants` VALUES ('97', '1', '38');
INSERT INTO `role_grants` VALUES ('98', '1', '39');
INSERT INTO `role_grants` VALUES ('99', '2', '64');
INSERT INTO `role_grants` VALUES ('100', '2', '63');
INSERT INTO `role_grants` VALUES ('101', '2', '62');
INSERT INTO `role_grants` VALUES ('102', '2', '61');
INSERT INTO `role_grants` VALUES ('103', '1', '61');
INSERT INTO `role_grants` VALUES ('104', '2', '65');
INSERT INTO `role_grants` VALUES ('105', '2', '66');
INSERT INTO `role_grants` VALUES ('106', '1', '66');
INSERT INTO `role_grants` VALUES ('107', '2', '73');
INSERT INTO `role_grants` VALUES ('108', '2', '74');
INSERT INTO `role_grants` VALUES ('109', '2', '75');

-- ----------------------------
-- Table structure for translations
-- ----------------------------
DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) NOT NULL,
  `row_id` int(10) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of translations
-- ----------------------------

-- ----------------------------
-- Table structure for translation_modules
-- ----------------------------
DROP TABLE IF EXISTS `translation_modules`;
CREATE TABLE `translation_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `field` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of translation_modules
-- ----------------------------
INSERT INTO `translation_modules` VALUES ('1', 'contents', 'contents', 'content');
INSERT INTO `translation_modules` VALUES ('2', 'pages', 'pages', 'page_title');
INSERT INTO `translation_modules` VALUES ('3', 'pages', 'pages', 'page_content');
INSERT INTO `translation_modules` VALUES ('4', 'blogs', 'blogs', 'blog_title');
INSERT INTO `translation_modules` VALUES ('11', 'catalogs', 'catalogs', 'catalog_title');
INSERT INTO `translation_modules` VALUES ('12', 'catalogs', 'catalogs', 'catalog_content');
INSERT INTO `translation_modules` VALUES ('13', 'forms', 'form_fields', 'field_title');
INSERT INTO `translation_modules` VALUES ('14', 'forms', 'form_field_values', 'value');
INSERT INTO `translation_modules` VALUES ('15', 'catalog_images', 'catalog_images', 'catalog_image_title');
INSERT INTO `translation_modules` VALUES ('17', 'materials', 'materials', 'material_title');
INSERT INTO `translation_modules` VALUES ('18', 'forms', 'form_field_select_options', 'option_title');
INSERT INTO `translation_modules` VALUES ('19', 'translations', 'translations_words', 'w_value');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `patronymic` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `about` text NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for user_grants
-- ----------------------------
DROP TABLE IF EXISTS `user_grants`;
CREATE TABLE `user_grants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `grant_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_grants
-- ----------------------------
INSERT INTO `user_grants` VALUES ('3', '3', '2', null);
INSERT INTO `user_grants` VALUES ('4', '3', null, '1');
INSERT INTO `user_grants` VALUES ('5', '3', null, '2');
INSERT INTO `user_grants` VALUES ('6', '3', null, '61');
INSERT INTO `user_grants` VALUES ('8', '3', null, '62');
INSERT INTO `user_grants` VALUES ('9', '4', null, '69');
INSERT INTO `user_grants` VALUES ('10', '4', null, '108');
INSERT INTO `user_grants` VALUES ('11', '4', null, '147');
INSERT INTO `user_grants` VALUES ('12', '4', null, '186');
INSERT INTO `user_grants` VALUES ('13', '4', null, '42');
INSERT INTO `user_grants` VALUES ('15', '4', null, '36');
INSERT INTO `user_grants` VALUES ('16', '4', null, '40');
INSERT INTO `user_grants` VALUES ('17', '4', null, '70');
INSERT INTO `user_grants` VALUES ('18', '4', null, '71');
INSERT INTO `user_grants` VALUES ('19', '4', null, '109');
INSERT INTO `user_grants` VALUES ('20', '4', null, '110');
INSERT INTO `user_grants` VALUES ('21', '4', null, '148');
INSERT INTO `user_grants` VALUES ('22', '4', null, '149');
INSERT INTO `user_grants` VALUES ('23', '4', null, '219');
INSERT INTO `user_grants` VALUES ('24', '4', null, '221');
INSERT INTO `user_grants` VALUES ('25', '4', null, '220');
INSERT INTO `user_grants` VALUES ('26', '4', null, '222');
INSERT INTO `user_grants` VALUES ('27', '4', null, '187');
INSERT INTO `user_grants` VALUES ('28', '4', null, '188');
INSERT INTO `user_grants` VALUES ('41', '4', null, '235');
INSERT INTO `user_grants` VALUES ('42', '4', null, '237');
INSERT INTO `user_grants` VALUES ('43', '4', null, '236');
INSERT INTO `user_grants` VALUES ('44', '4', null, '238');
INSERT INTO `user_grants` VALUES ('45', '4', null, '239');
INSERT INTO `user_grants` VALUES ('46', '4', null, '241');
INSERT INTO `user_grants` VALUES ('47', '4', null, '240');
INSERT INTO `user_grants` VALUES ('48', '4', null, '242');
INSERT INTO `user_grants` VALUES ('49', '4', null, '356');
INSERT INTO `user_grants` VALUES ('50', '4', null, '358');
INSERT INTO `user_grants` VALUES ('51', '4', null, '357');
INSERT INTO `user_grants` VALUES ('52', '4', null, '359');
INSERT INTO `user_grants` VALUES ('53', '4', null, '368');
INSERT INTO `user_grants` VALUES ('54', '4', null, '370');
INSERT INTO `user_grants` VALUES ('55', '4', null, '369');
INSERT INTO `user_grants` VALUES ('56', '4', null, '371');

-- ----------------------------
-- Table structure for qbit_versions
-- ----------------------------
DROP TABLE IF EXISTS `qbit_versions`;
CREATE TABLE `qbit_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) DEFAULT NULL,
  `type` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of qbit_versions
-- ----------------------------
INSERT INTO `qbit_versions` VALUES ('1', '3.00', 'f');
INSERT INTO `qbit_versions` VALUES ('2', '3.01', 'f');

-- ----------------------------
-- Table structure for update_files
-- ----------------------------
DROP TABLE IF EXISTS `update_files`;
CREATE TABLE `update_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of update_files
-- ----------------------------

-- ----------------------------
-- Table structure for update_license_checks
-- ----------------------------
DROP TABLE IF EXISTS `update_license_checks`;
CREATE TABLE `update_license_checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license_key` varchar(255) DEFAULT NULL,
  `check_datetime` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of update_license_checks
-- ----------------------------

-- ----------------------------
-- Table structure for update_licenses
-- ----------------------------
DROP TABLE IF EXISTS `update_licenses`;
CREATE TABLE `update_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license_key` varchar(255) DEFAULT NULL,
  `activation_datetime` datetime DEFAULT NULL,
  `deactivation_datetime` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `file_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of update_licenses
-- ----------------------------


-- ----------------------------
-- Table structure for arhlog
-- ----------------------------
DROP TABLE IF EXISTS `arhlog`;
CREATE TABLE `arhlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL,
  `hash_group` varchar(255) NOT NULL DEFAULT '',
  `reg_date` datetime NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  `priority` smallint(6) NOT NULL,
  `type` varchar(1) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `conditions` text,
  `data` mediumtext,
  `actuality` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_hash_group` (`hash_group`),
  KEY `idx_resource_name` (`resource_name`),
  KEY `idx_session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
