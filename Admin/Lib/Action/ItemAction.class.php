<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class ItemAction extends CommonAction {
	
	function index($status=1){
	
		if(isset($_GET['do'])){
			$this->edit($_GET['id']);exit;
		}
		
		$Model = D('ItemView');
		$Tags  = D('TagsRelationshipView');
		
		$keyword = trim($_GET['keyword']);
		$tags_id = trim($_GET['tags_id']);
		$orderby = trim($_GET['orderby']);
		$sort    = trim($_GET['sort']);
		$where = "1=1";
		if(!empty($keyword)) $where .= " AND title LIKE '%$keyword%' OR host LIKE '%$keyword%'";
		if(!empty($tags_id)) $where .= " AND tags_relationship.tags_type_id =".$tags_id;
		$order = empty($orderby)?"sort_order DESC,id DESC":"{$orderby} {$sort}";
		
		import('ORG.Util.Page');
		$count = $Model->where($where)->count('distinct(id)');
		$page  = new Page($count,20);
		$list  = $Model->where($where)->group('item.id')->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();

		$show  = $page->show();
		foreach($list as $k=>$v){
			$list[$k]['tags'] = $Tags->where('item_id='.$v['id'])->select();
		}
		
		//分类列表
		$tags = D('TagsView')->where('type=2')->order('pid asc,sort_order desc,tags_type_id ASC')->select();
		$tags = list_to_level($tags);
		$this->assign('tags',$tags);
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display($status==1?'index':'check');
	}

	function check(){
		$this->index(2);
	}
	
	//内容编辑
	function edit($id=''){
		
		if(isset($_GET['fieldId']) && isset($_GET['fieldValue'])){
			$this->checkURL($_GET['fieldValue'],$id); //ajax 检验网址
		}
		
		//分类列表
		$tags = D('TagsView')->where('type=2')->order('pid asc,sort_order desc,tags_type_id ASC')->select();
		$tags = list_to_level($tags);
		
		if(!empty($id)){
			$where = array('id'=>(int)$id);
			$info  = M('Item')->where($where)->find();
			
			//关联的分类
			$cate  = M('TagsRelationship')->where('item_id='.$id)->select();
			foreach($cate as $li){ $itemTags[] = $li['tags_type_id'];} 
		}

		$this->assign('tags',$tags);
		$this->assign('itemTags',$itemTags);
		$this->assign('info',$info);
		$this->assign('posHome',getFields('Category','name',$_GET['cid']));
		$this->display('edit');
	}
	
	//分类栏目
	public function category(){
		$action = 'TagsType/category';
		R($action);
		$this->display($action);
	}

	//标签
	public function tags(){
		$action = 'TagsType/tags';
		R($action);
		$this->display($action);
	}
	
	function checkURL($fieldValue,$id=0){
		$Model = M('Item');
		$flag  = false;
		$url   = parse_url($fieldValue);
		if(isset($url['host'])){
			$where = "host='{$url['host']}'";
			if($id) $where .= " AND id !=".$id;
			$exist = $Model->where($where)->getField('url');
			if(!$exist)$flag = true;
		}
		echo json_encode(array('url',$flag));exit;
	}

	//批量删除
	public function deleteAll(){
		$Model = D('Item');
		foreach($_POST['id'] as $id){
			$Model->delete((int)$id);
		}
		$this->success('删除成功');
	}

	function icon(){
		$icon = array('glass','music','search','envelope-alt','heart','star','star-empty','user','film','th-large','th','th-list','ok','remove','zoom-in','zoom-out','power-off','signal','gear','trash','home','file-alt','time','road','download-alt','download','upload','inbox','play-circle','rotate-right','refresh','list-alt','lock','flag','headphones','volume-off','volume-down','volume-up','qrcode','barcode','tag','tags','book','bookmark','print','camera','font','bold','italic','text-height','text-width','align-left','align-center','align-right','align-justify','list','indent-left','indent-right','facetime-video','picture','pencil','map-marker','adjust','tint','edit','share','check','move','step-backward','fast-backward','backward','play','pause','stop','forward','fast-forward','step-forward','eject','chevron-left','chevron-right','plus-sign','minus-sign','remove-sign','ok-sign','question-sign','info-sign','screenshot','remove-circle','ok-circle','ban-circle','arrow-left','arrow-right','arrow-up','arrow-down','mail-forward','resize-full','resize-small','plus','minus','asterisk','exclamation-sign','gift','leaf','fire','eye-open','eye-close','warning-sign','plane','calendar','random','comment','magnet','chevron-up','chevron-down','retweet','shopping-cart','folder-close','folder-open','resize-vertical','resize-horizontal','bar-chart','twitter-sign','facebook-sign','camera-retro','key','gears','comments','thumbs-up-alt','thumbs-down-alt','star-half','heart-empty','signout','linkedin-sign','pushpin','external-link','signin','trophy','github-sign','upload-alt','lemon','phone','unchecked','bookmark-empty','phone-sign','twitter','facebook','github','unlock','credit-card','rss','hdd','bullhorn','bell','certificate','hand-right','hand-left','hand-up','hand-down','circle-arrow-left','circle-arrow-right','circle-arrow-up','circle-arrow-down','globe','wrench','tasks','filter','briefcase','fullscreen','group','link','cloud','beaker','cut','copy','paperclip','save','sign-blank','reorder','list-ul','list-ol','strikethrough','underline','table','magic','truck','pinterest','pinterest-sign','google-plus-sign','google-plus','money','caret-down','caret-up','caret-left','caret-right','columns','sort','sort-down','sort-up','envelope','linkedin','rotate-left','legal','dashboard','comment-alt','comments-alt','bolt','sitemap','umbrella','paste','lightbulb','exchange','cloud-download','cloud-upload','user-md','stethoscope','suitcase','bell-alt','coffee','food','file-text-alt','building','hospital','ambulance','medkit','fighter-jet','beer','h-sign','plus-sign-alt','double-angle-left','double-angle-right','double-angle-up','double-angle-down','angle-left','angle-right','angle-up','angle-down','desktop','laptop','tablet','mobile-phone','circle-blank','quote-left','quote-right','spinner','circle','mail-reply','github-alt','folder-close-alt','folder-open-alt','expand-alt','collapse-alt','smile','frown','meh','gamepad','keyboard','flag-alt','flag-checkered','terminal','code','reply-all','mail-reply-all','star-half-full','location-arrow','crop','code-fork','unlink','question','info','exclamation','superscript','subscript','eraser','puzzle-piece','microphone','microphone-off','shield','calendar-empty','fire-extinguisher','rocket','maxcdn','chevron-sign-left','chevron-sign-right','chevron-sign-up','chevron-sign-down','html5','css3','anchor','unlock-alt','bullseye','ellipsis-horizontal','ellipsis-vertical','rss-sign','play-sign','ticket','minus-sign-alt','check-minus','level-up','level-down','check-sign','edit-sign','external-link-sign','share-sign','compass','collapse','collapse-top','expand','euro','gbp','dollar','rupee','yen','renminbi','won','bitcoin','file','file-text','sort-by-alphabet','sort-by-alphabet-alt','sort-by-attributes','sort-by-attributes-alt','sort-by-order','sort-by-order-alt','thumbs-up','thumbs-down','youtube-sign','youtube','xing','xing-sign','youtube-play','dropbox','stackexchange','instagram','flickr','adn','bitbucket','bitbucket-sign','tumblr','tumblr-sign','long-arrow-down','long-arrow-up','long-arrow-left','long-arrow-right','apple','windows','android','linux','dribbble','skype','foursquare','trello','female','male','gittip','sun','moon','archive','bug','vk','weibo','renren',);
		$this->assign('icon',$icon);
		$this->display();
	}
}
?>