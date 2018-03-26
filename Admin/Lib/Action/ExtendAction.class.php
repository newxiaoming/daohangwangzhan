<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class ExtendAction extends CommonAction {
	
	function index($module='link'){
		if(isset($_GET['do'])){
			$this->edit($module);exit;
		}
		$Model = M(ucfirst($module));
		import('ORG.Util.Page');
		$count = $Model->where($where)->count();
		$page  = new Page($count,15);
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('sort_order desc')->select();
		$show  = $page->show();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->assign('action',$module);
		$this->display($module);
	}
	
	function edit($module =''){
		$id = (int)$_GET['id'];
		if($id){
			$Model = M(ucfirst($module));
			$info  = $Model->where('id='.$id)->find();
			$this->assign('info',$info);
		}
		$this->display('Extend:'.$module.'Edit');
	}
	
	function delete(){
		$id    = (int)$_GET['id'];
		$Model = M(ucfirst($_GET['module']));
		
		if(empty($id) || !is_numeric($id)) $this->ajaxReturn(null,'非法操作！',0);
		$Model->delete($id);
		$this->ajaxReturn(null,'删除成功！',1);
	}

	public function __call($name,$arguments) {
        $this->index($name);
    }
}
?>