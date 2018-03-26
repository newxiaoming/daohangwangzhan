<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class UserAction extends CommonAction {
	
	/*
	 * 用户列表
	 */
	function index($is_apply=0){
		
		$id = (int)$_GET['id'];
		$do = trim($_GET['do']);
		if(!empty($do)){
			$this->info($id);
			$this->display($do);exit;
		}
		$keyword = trim($_GET['keyword']);
		$orderby = trim($_GET['orderby']);
		$sort    = trim($_GET['sort']);
		$role    = trim($_GET['role']);
		
		$Model = D('User');
		import('ORG.Util.Page');
		$where = "is_delete=0 AND is_apply=".$is_apply;
	
		if(!empty($keyword)) $where .= " AND (username LIKE '%{$keyword}%' OR mobile LIKE '{$keyword}%' OR nickname LIKE '{$keyword}%')";
		if(!empty($role)) $where .= " AND role = '{$role}'";
		$order = empty($orderby)?"id desc":"{$orderby} {$sort}";
		
		$count = $Model->where($where)->count();
		$page  = new Page($count,15);
		
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
		$show  = $page->show();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display(__FUNCTION__);
    } 
	
	//查看用户信息
	function info(int $user_id){
		$info = M('User')->where('id='.$user_id)->find();
		$this->assign('info',$info);
	}
	
	
}
?>