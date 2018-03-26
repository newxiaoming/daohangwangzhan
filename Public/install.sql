
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `cc_advert`
-- ----------------------------
DROP TABLE IF EXISTS `cc_advert`;
CREATE TABLE `cc_advert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` varchar(100) NOT NULL COMMENT '名称',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `link` varchar(255) NOT NULL COMMENT '链接地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `target` enum('_blank','_self') NOT NULL,
  `description` mediumtext NOT NULL COMMENT '描述',
  `sort_order` mediumint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) NOT NULL,
  `type` enum('幻灯片','广告') NOT NULL DEFAULT '幻灯片' COMMENT '1幻灯片，2广告',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='广告幻灯片表-alizi.net';

-- ----------------------------
-- Table structure for `cc_item`
-- ----------------------------
DROP TABLE IF EXISTS `cc_item`;
CREATE TABLE `cc_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '标题名称',
  `host` varchar(255) NOT NULL COMMENT '主域名',
  `url` varchar(255) NOT NULL COMMENT '网址',
  `icon` varchar(100) NOT NULL COMMENT 'favicon',
  `logo` varchar(100) NOT NULL COMMENT 'logo',
  `description` varchar(255) NOT NULL COMMENT '描述信息',
  `status` tinyint(1) NOT NULL COMMENT '状态。1有效，0无效',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序。倒序',
  `add_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `click` int(10) NOT NULL DEFAULT '0' COMMENT '点击次数',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='项目表';

-- ----------------------------
-- Table structure for `cc_link`
-- ----------------------------
DROP TABLE IF EXISTS `cc_link`;
CREATE TABLE `cc_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '名称',
  `link` varchar(255) NOT NULL COMMENT '链接',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo图片',
  `sort_order` mediumint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='友情链接表';


-- ----------------------------
-- Table structure for `cc_message`
-- ----------------------------
DROP TABLE IF EXISTS `cc_message`;
CREATE TABLE `cc_message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `content` text NOT NULL COMMENT '内容',
  `sort_order` mediumint(5) NOT NULL DEFAULT '0',
  `add_ip` varchar(15) NOT NULL,
  `add_time` int(10) NOT NULL COMMENT '时间',
  `update_time` int(10) NOT NULL COMMENT '回复时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言板表';

-- ----------------------------
-- Records of cc_message
-- ----------------------------

-- ----------------------------
-- Table structure for `cc_setting`
-- ----------------------------
DROP TABLE IF EXISTS `cc_setting`;
CREATE TABLE `cc_setting` (
  `name` varchar(50) NOT NULL COMMENT '键',
  `alias` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态。1可见，0不可见',
  `value` text NOT NULL COMMENT '值',
  `default_value` text NOT NULL COMMENT '默认值',
  `groups` varchar(50) NOT NULL COMMENT '分组',
  `sort_order` mediumint(5) NOT NULL DEFAULT '100' COMMENT '排序',
  `tags` enum('text','checkbox','radio','textarea','select','file','password','extend') NOT NULL DEFAULT 'text',
  `width` mediumint(50) NOT NULL DEFAULT '30',
  `height` mediumint(5) NOT NULL DEFAULT '25',
  `decription` text NOT NULL,
  `separator` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分隔',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统设置表';

-- ----------------------------
-- Records of cc_setting
-- ----------------------------
INSERT INTO `cc_setting` VALUES ('title', '网站标题', '1', '289w设计师网址导航', '', 'basic_info', '3', 'text', '50', '0', '', '0');
INSERT INTO `cc_setting` VALUES ('keywords', '网站关键词', '1', '前端设计，UI设计，网页设计，素材下载，设计师网站导航，设计师网址导航，设计导航，设计师导航', '', 'basic_info', '5', 'text', '80', '0', '', '0');
INSERT INTO `cc_setting` VALUES ('logo', '网站Logo', '1', '', '', 'basic_info', '6', 'file', '50', '0', '', '0');
INSERT INTO `cc_setting` VALUES ('description', '网站描述', '1', '设计师，Web工程师，创业者的网址导航，行业领域精英人士必备利器。', '', 'basic_info', '7', 'textarea', '65', '3', '', '0');
INSERT INTO `cc_setting` VALUES ('sub_title', '副标题', '1', 'IT行业精英人士必备导航，一站在手，天下我有！', '', 'basic_info', '8', 'text', '80', '0', '', '0');
INSERT INTO `cc_setting` VALUES ('item_hot_show', '首页推荐', '1', '1', 'array(\'1\'=>\'显示\',\'0\'=>\'关闭\',)', '网站设置', '22', 'radio', '30', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('item_hot_num', '首页推荐数量', '1', '27', '27', '网站设置', '23', 'text', '5', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('DEFAULT_THEME', '网站主题', '1', 'Blue', 'array(\'Black\'=>\'黑色主题\',\'Blue\'=>\'蓝色主题\',)', '网站设置', '20', 'select', '80', '0', '', '0');
INSERT INTO `cc_setting` VALUES ('footer', '网站底部', '1', 'Copyright © 2014 www.289w.com All Rights Reserved ', '', 'basic_info', '8', 'textarea', '100', '6', '', '0');
INSERT INTO `cc_setting` VALUES ('width', '网站宽度', '1', '1200', '', '网站设置', '20', 'text', '5', '25', '单位是px', '0');
INSERT INTO `cc_setting` VALUES ('recommend_img_width', '首页推荐图片宽度', '1', '100', '', '网站设置', '30', 'text', '5', '25', '宽度最好在100-200之间', '1');
INSERT INTO `cc_setting` VALUES ('URL_MODEL', '网站运行模式', '1', '0', 'array(\'0\'=>\'动态模式\',\'2\'=>\'伪静态模式\',\'1\'=>\'PATHINFO模式\')', '网站设置', '20', 'select', '30', '25', '', '1');
INSERT INTO `cc_setting` VALUES ('category_num', '首页分类数量', '1', '100', '', '网站设置', '31', 'text', '5', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('category_show_description', '首页分类描述', '1', '1', 'array(\'1\'=>\'显示\',\'0\'=>\'关闭\',)', '网站设置', '32', 'radio', '30', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('slideshow', '首页显示幻灯片', '1', '1', 'array(\'1\'=>\'显示\',\'0\'=>\'关闭\',)', '网站设置', '33', 'radio', '30', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('qrcode', '上传二维码', '1', '', '', '网站设置', '100', 'file', '30', '25', '', '0');
INSERT INTO `cc_setting` VALUES ('sidenav', '显示则栏导航条', '1', '1', 'array(\'1\'=>\'显示\',\'0\'=>\'关闭\',)', '网站设置', '34', 'radio', '30', '25', '', '0');

-- ----------------------------
-- Table structure for `cc_tags`
-- ----------------------------
DROP TABLE IF EXISTS `cc_tags`;
CREATE TABLE `cc_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `alias` varchar(50) NOT NULL COMMENT '别名，只能用字母',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签表';


-- ----------------------------
-- Table structure for `cc_tags_relationship`
-- ----------------------------
DROP TABLE IF EXISTS `cc_tags_relationship`;
CREATE TABLE `cc_tags_relationship` (
  `item_id` bigint(20) NOT NULL,
  `tags_type_id` bigint(20) NOT NULL,
  PRIMARY KEY (`item_id`,`tags_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签关联表';


-- ----------------------------
-- Table structure for `cc_tags_type`
-- ----------------------------
DROP TABLE IF EXISTS `cc_tags_type`;
CREATE TABLE `cc_tags_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tags_id` bigint(20) NOT NULL,
  `tags_pid` int(20) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '标签分类。1tag，2category',
  `icon` varchar(50) NOT NULL COMMENT '图标',
  `count` bigint(20) NOT NULL COMMENT '统计数量',
  `sort_order` mediumint(5) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL,
  `keywords` varchar(100) NOT NULL COMMENT '关键词',
  `description` varchar(255) NOT NULL COMMENT '描述信息',
  PRIMARY KEY (`id`),
  KEY `tags_id` (`tags_id`),
  KEY `tags_pid` (`tags_pid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签类型';


-- ----------------------------
-- Table structure for `cc_user`
-- ----------------------------
DROP TABLE IF EXISTS `cc_user`;
CREATE TABLE `cc_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `role` enum('admin','member') NOT NULL DEFAULT 'admin' COMMENT '用户角色',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态。1启用，2禁用',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `qq` varchar(50) NOT NULL DEFAULT '' COMMENT 'qq',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT 'Email',
  `info` mediumtext NOT NULL,
  `login_ip` char(16) NOT NULL,
  `login_time` datetime NOT NULL,
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户表';
