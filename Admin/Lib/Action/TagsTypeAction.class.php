<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class TagsTypeAction extends CommonAction {
	
	function tags(){	
		$type = 1;
		if(isset($_GET['do'])){
			$this->edit($_GET['id'],$type);exit;
		}
		
		$Model = D('TagsView');
		import('ORG.Util.Page');
		$where = 'type='.$type;
		$count = $Model->where($where)->count();
		$page  = new Page($count,15);
		$list  = $Model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('sort_order desc')->select();
		$show  = $page->show();

		$this->assign('list',$list);
		$this->assign('page',$show);
	}
	
	function category(){
		$type = 2;
		if(isset($_GET['do'])){
			$this->edit($_GET['id'],$type);exit;
		}
		$Model = D('TagsView');
		$where = 'tags_type.type='.$type;
		$list  = $Model->where($where)->order('pid asc,sort_order desc,tags_type_id ASC')->select();
		$list  = list_to_level($list);

		$this->assign('list',$list);
	}
	
	//分类和标签编辑
	function edit($id='',$type=1){
		$Model = D('TagsView');
		$where = 'type='.$type;
		$list  = $Model->where($where)->order('pid ASC,sort_order desc,tags_type_id ASC')->select();
		$list  = list_to_level($list);
		
		if(!empty($id)){
			$info = $Model->where('tags_type.id='.$id)->find();
			$this->assign('info',$info);
		}
		
		$this->assign('type',$type);
		$this->assign('list',$list);
		$this->display('TagsType:edit');
	}
	
	//删除分类
	public function delete(){
		$Model = D('TagsType');
		$Model->delete();
	} 
}
?>