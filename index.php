<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
//调试模式
define('APP_DEBUG',true);

//页面压缩输出
ob_start('ob_gzhandler');

//定义缓存目录
define( 'RUNTIME_PATH' , './Public/Cache/Home/' );
define( 'CONF_PATH' , './Public/Common/' );
define( 'LANG_PATH' , './Public/Common/' );
define( 'COMMON_PATH' , './Public/Common/' );
define( 'VERSION' , 'V2.0' );

//项目必须
define('APP_NAME','Home');
define('APP_PATH','./Home/');
require 'Public/ThinkPHP/ThinkPHP.php';
?>