<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class CommonAction extends Action {

	protected $uid;      //用户id
	protected $username; //用户名
	
    function _initialize(){
		//header('content-type:text/html;charset=utf-8');
		
		if(empty($_SESSION['user']['id'])){//未登陆
			$this->display('Index::login');exit;
		}
		$this->uid  = (int)$_SESSION['user']['id'];
		$this->role = $_SESSION['user']['role'];
		$this->username = $_SESSION['user']['username'];
    }
	
	//添加,删除,修改操作
	function proccess($module='') {
		$module = empty($module)?$this->getActionName():$module;
		$status = 0;
		$Model = D($module);
	
		if($_REQUEST['do']==='delete'){ //删除
			$status = $Model->delete((int)$_REQUEST['id']);
		}else{
			if($vo = $Model->create($_REQUEST)){
				if(empty($vo['id'])){
					$id = $Model->add();
					$status = $id?1:0;
				}else{
					$status = $Model->save();
				}
			}
		}
		
		//print_r($vo);echo $Model->_sql();
		$info = $status?lang('action_success'):lang('action_failure_colon').$Model->getError();
		$this->ajaxReturn(null,$info,(int)$status);
	}
	
}
?>