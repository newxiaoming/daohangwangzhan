<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
$config= array(
	'URL_MODEL'           => 0,    // URL模式，2静态模式
	'SHOW_PAGE_TRACE'     => false, // 调式跟踪信息
	'DATA_CACHE_PATH'     => './Public/Cache/', // 缓存路径设置 (仅对File方式缓存有效)
  	'DEFAULT_THEME' => 'Black',//模板主题
  	'TMPL_FILE_DEPR' => '_',
	
	'HTML_CACHE_ON'     => false, //静态缓存开启
	'HTML_CACHE_RULES'  => array('Index:'=>array('../../Index/{:action}',1),),

	'TOKEN_ON'=>false,  // 是否开启令牌验证
	'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
	 
	'DEFAULT_AJAX_RETURN' => 'json',                          // 默认ajaxReturn返回json格式
	'TMPL_ACTION_ERROR'   => 'Public/success.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public/success.html', // 默认成功跳转对应的模板文件
	
	'ROOT_FILE'   => '', //网站所在目录。根目录请留空，如果放在二级目录web，则写web/
	'INFO_MODULE' => array(1=>'单页信息',2=>'文章列表',3=>'图片列表'), //信息模型
);
$db = include("config.db.php");
if(!empty($db)) $config = array_merge($config,$db);
return $config;
?>