<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
class EmptyAction extends Action {
	
	public function index(){
          	//根据当前模块名来判断要执行那个城市的操作
      	$method = MODULE_NAME;
      	header('HTTP/1.1 301 Moved Permanently'); 
      	header('Location: '.U('Index/'.$method));  
      }

      function __call($method,$arg){
      	header('HTTP/1.1 301 Moved Permanently'); 
      	header('Location: '.U('Index/'.$method));  
      }

}