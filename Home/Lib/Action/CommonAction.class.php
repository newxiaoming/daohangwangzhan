<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
class CommonAction extends Action{
	
	public function _initialize(){

 		if(!file_exists('./Public/install.lock')) header('location:'.U('Install/index'));
		$Cache = Cache::getInstance('File',array('expire'=>10));//使用缓存
		
		//导航菜单
		global $navigation;
		$navigation = $Cache->get('navigation'); 
		if(empty($navigation)){
			$navigation = M('Tags')->query('SELECT tags.id,tags.name,tags.alias,tags_type.icon,tags_type.id AS tags_type_id FROM __TABLE__ tags INNER JOIN '.C('DB_PREFIX').'tags_type tags_type ON tags.id=tags_type.tags_id WHERE tags_type.type=2 AND tags_type.tags_pid=0 ORDER BY tags_type.sort_order DESC'); 
			$Cache->set('navigation',$navigation);  
		}

		//系统设置
		$this->config = $this->config();

		//友情链接
		$link = $Cache->get('link'); 
		if(empty($link)){
			$link = M('Link')->order('sort_order DESC')->select(); 
			foreach($setting as $v) $config[$v['name']] = $v['value'];	
			$Cache->set('link',$link);  
		}
		
		$this->assign('navigation',$navigation);
		$this->assign('link',$link);
		$this->assign('config',$this->config);
	}

	public function config(){
		$config = cache('config');
		if(empty($config)){
			$Model = M('Setting');
			$list = $Model->field(array('name','value'))->where('status=1')->order('groups asc,sort_order asc')->select();
			foreach($list as $li) $config[$li['name']] = $li['value'];
			cache('config',$config,8640000);
		}
		return $config;
	}
}
?>