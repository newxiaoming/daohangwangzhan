<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
class IndexAction extends CommonAction {
	
	public function _initialize(){
		parent::_initialize();
		
		$name = str_replace('/','',ACTION_NAME);
		
		 //手持设备浏览
		if(isMobile()){ 
			C('URL_MODEL',0);
			C('HTML_CACHE_ON',false);
			$this->mobile($name);exit; 
		}else{
			if(method_exists($this,$name)){
				$this->$name();
			}else{
				$this->pc($name);
			}
			exit;
		}
	}
    public function index(){
		$Model    = M('Item');
		$TagsType = M('TagsType');

		$list = array();
		$navigation = $this->navigation;
		$limit = intval($this->config['category_num'])>0?$this->config['category_num']:42;
		foreach($navigation as $li){
			$li['category'] = $TagsType->query('SELECT tags.id,tags.name,tags.alias FROM __TABLE__ tags_type INNER JOIN '.C('DB_PREFIX').'tags tags ON tags_type.tags_id=tags.id WHERE tags_type.tags_pid='.$li['id'].' ORDER BY tags_type.sort_order DESC,tags.id ASC');
			
			$li['list'] = $Model->query('SELECT item.title,item.url,item.icon,item.description,item.is_hot FROM __TABLE__ item INNER JOIN '.C('DB_PREFIX').'tags_relationship tags_relationship ON item.id=tags_relationship.item_id WHERE item.status=1 AND tags_relationship.tags_type_id='.$li['tags_type_id'].' ORDER BY item.sort_order DESC,item.id ASC LIMIT '.$limit);
			$list[] = $li;
		}
		$this->seoSetting($tags);
		$this->assign('list',$list);
        	$this->display();
    }

	//魔术棒解析扩展工具
	private function pc($name,$arguments) {
		if(!ctype_alpha($name)){
			//记录非法请求并跳转到首页
			@file_put_contents('./Public/Cache/Access.txt',var_export(array('Time'=>date('Y-m-d H:i:s'),'URLs'=>$_SERVER['REQUEST_URI'],'IP'=>get_client_ip()),true)."\n",FILE_APPEND);
			//header('location:/page404.html');
			header('HTTP/1.1 404 Not Found');  
			$this->display('404');exit;
		}
		$list     = array();
		$Model    = M('Tags');
		$Item     = M('Item');
		
		$tags  = D('TagsView')->where(array('alias' => $name))->find();
	
		if($tags['pid']==0){
			$hasSubNav = M('TagsType')->where('tags_pid='.$tags['id'])->getField('id');
			if($hasSubNav){
				$map   = 'tags_type.tags_pid='.$tags['id'];
				$limit = ' LIMIT 42';
			}else{
				$map   = 'tags_type.tags_id='.$tags['id'];
			}
		}else{
			$map   = 'tags_type.id='.$tags['tags_type_id'];
			$name  = $Model->where('id='.$tags['pid'])->getField('alias');
		}
		$menu = $Model->query('SELECT tags.id,tags.name,tags.alias,tags_type.icon,tags_type.id AS tags_type_id FROM __TABLE__ tags INNER JOIN '.C('DB_PREFIX').'tags_type tags_type ON tags.id=tags_type.tags_id WHERE tags_type.type=2 AND '.$map.' ORDER BY tags_type.sort_order DESC'); 

		foreach($menu as $li){
			$li['list'] = $Item->query('SELECT item.title,item.url,item.icon,item.description,item.is_hot FROM __TABLE__ item INNER JOIN '.C('DB_PREFIX').'tags_relationship tags_relationship ON item.id=tags_relationship.item_id WHERE item.status=1 AND tags_relationship.tags_type_id='.$li['tags_type_id'].' ORDER BY item.sort_order DESC,item.id ASC '.$limit);
			$list[] = $li;
		}

		$this->seoSetting($tags);

		$this->assign('list',$list);
		$this->assign('actionName',$name);
		$this->display('category');
    }
	
	private function mobile($name){
		$perpage  = 20;
		$page     = isset($_POST['p'])?($_POST['p']*$perpage):0;
		$Model    = M('Item');
		$TagsType = M('TagsType');
		if($name=='index'){
			$list = $Model->field('id','title','url','icon','logo','description')->where('status=1')->order('sort_order desc,id desc')->limit("{$page},{$perpage}")->select();
		}else{
			$tags_type_id = D('TagsView')->where(array('alias' => $name))->getField('tags_type_id'); 
			$list = $Model->query('SELECT item.title,item.url,item.icon,item.description FROM __TABLE__ item INNER JOIN '.C('DB_PREFIX').'tags_relationship tags_relationship ON item.id=tags_relationship.item_id WHERE item.status=1 AND tags_relationship.tags_type_id='.$tags_type_id.' ORDER BY item.sort_order DESC,item.id ASC LIMIT '.$page.','.$perpage);
		}
		
		if(IS_POST){
			$loadNext = count($list)>=$perpage?1:0;
			$this->ajaxReturn($list,1,$loadNext);exit;
		}
		
		global $navigation;
		$category = $TagsType->query('SELECT tags.id,tags.name,tags.alias FROM __TABLE__ tags_type INNER JOIN '.C('DB_PREFIX').'tags tags ON tags_type.tags_id=tags.id WHERE tags_type.tags_pid='.$li['tags_type_id'].' ORDER BY tags_type.sort_order DESC,tags.id ASC');
		
		$this->seoSetting($category);
		$this->assign('actionName',$name);
		$this->assign('new',$new);
		$this->assign('list',$list);
		$this->display('mobile');
	}

	//搜索
	public function search(){
		$Item = M('Item');
		$kw = trim($_GET['kw']);
		$where = "status=1";
		if(!empty($kw)) $where .=" AND `title` LIKE '%{$kw}%' OR `host` LIKE '%{$kw}%'  OR `description` LIKE '%{$kw}%'  ";
		$list = $Item->where($where)->limit(50)->select();
		$this->assign('list',$list);
		$this->display();
	}

	//seo设置
	private function seoSetting($tags){
		$config = $this->config;
		$seo = array(
			'title' => empty($tags['title'])?$config['title']:$tags['title']." - ".$config['title'],
			'keywords' => empty($tags['keywords'])?$config['keywords']:$tags['keywords'],
			'description' => empty($tags['description'])?$config['description']:$tags['description'],
		);
		$this->assign('seo',$seo);
	}

	//留言
	public function message(){
		if(IS_POST){
			$data = array(
				'email'=>strip_tags($_POST['email']),
				'content'=>strip_tags($_POST['content']),
				'add_ip'=>get_client_ip(),
				'add_time'=>time(),
			);
			$status = M('Message')->add($data);
			$this->ajaxReturn(null,'留言成功',$status);
		}else{
			$this->display();
		}
	}

	//统计点击数
	public function click(){
		M('Item')->where(array('url'=>htmlentities($_POST['url'])))->setInc('click',1);
	}
}