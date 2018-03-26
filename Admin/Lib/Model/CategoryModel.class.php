<?php 
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class CategoryModel extends Model {

	protected $_validate = array(
		array('name', 'require', '标题名称不能为空！',1,'',1),
		array('alias', 'checkAlias', '别名已有存在！',1,'callback',3),
		array('module', 'checkModule', '该栏目不能添加下级栏目！',1,'callback',3),
	);
	protected $_auto = array(
		array('create_time', 'time', 1, 'function'),
	);
	
	//子栏目的别名和模型跟父级一致
	function checkParent(&$data){
		if(!empty($data['pid'])){
			$parent = M('Category')->where('id='.$_POST['pid'])->find();
			$data['alias']  = $parent['alias'];
			$data['module_id'] = $parent['module_id'];
		}
	}
	
	//检验顶级栏目别名是否重复
	function checkAlias(){
		if(empty($_POST['pid'])){
			$where = array(
				'pid'   => 0,
				'alias' => trim($_POST['alias']),
			);
			if(isset($_POST['id']))  $where['id'] = array('neq',$_POST['id']);//更新时检查id
			$flag = M('Category')->where($where)->getField('id');
			if($flag) return false;
		}
	}
	
	//module_id = 1 不能添加下级栏目
	function checkModule(){
		if(!empty($_POST['pid'])){
			$where = array(
				'id' => $_POST['pid'],
			);
			$module_id = M('Category')->where($where)->getField('module_id');
			if($module_id==1){
				return false;
			}
		}
	}
	
	function _before_update(&$data, $options){
		$data['is_nav'] = isset($data['is_nav'])?1:0;
		$this->checkParent($data);
	}
	function _before_insert(&$data, $options){
		$this->checkParent($data);
	}
	function _after_update(&$data, $options){}
	function _after_insert(&$data, $options){}
}
?>