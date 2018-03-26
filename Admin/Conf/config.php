<?php
$config = include("Public/Common/config.php");
$admin = array(
	'URL_MODEL' => 0,
	'HTML_CACHE_ON' => false,
	'DEFAULT_THEME' => '',
	'TMPL_FILE_DEPR' => '/',
);
return array_merge($config,$admin);
?>