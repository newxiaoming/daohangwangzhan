<?php 
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class ItemModel extends Model {

	protected $_validate = array(
		array('title', 'require', '标题不能为空！',1,'',1),
		array('url', 'require', 'URL不能为空！',1,'',1),
	);
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
		array('update_time', 'time', 2, 'function'),
		array('is_hot', 'intval', 2, 'function'),
		array('host', 'getHost', 3, 'callback'),
	);
	
	function getHost(){
		//获取主域名
		$url = parse_url($_POST['url']);
		return $url['host'];
	}
	function _after_insert(&$data, $options){
		$TagsType = M('TagsType');
		$TagsRelationship = M('TagsRelationship');
		$where = array('item_id'=>$data['id']);
		$TagsRelationship->where($where)->delete();
		foreach($_POST['tags'] as $tags){
			$TagsRelationship->add(array('item_id'=>$data['id'],'tags_type_id'=>$tags));
			
			//统计数量
			$count = array('id'=>$tags,'count'=>$TagsRelationship->where('tags_type_id='.$tags)->count());
			$TagsType->save($count);
		}
	}
	function _after_update(&$data, $options){
		$TagsType = M('TagsType');
		$TagsRelationship = M('TagsRelationship');
		
		$where = array('item_id'=>$data['id']);
		$tags  = $TagsRelationship->where($where)->select();//删除之前查询
		$TagsRelationship->where($where)->delete();
		
		foreach($_POST['tags'] as $tid){
			$addData = array('item_id'=>$data['id'],'tags_type_id'=>$tid);
			array_push($tags,$addData);
			$TagsRelationship->add($addData);
		}
		
		//统计数量
		foreach($tags as $li){
			$count = $TagsRelationship->where('tags_type_id='.$li['tags_type_id'])->count();
			$save = array('id'=>$li['tags_type_id'],'count'=>$count);
			$TagsType->save($save);
		}
	}
	function _after_delete(&$data, $options){
		
		$TagsRelationship = M('TagsRelationship');
		$where = array('item_id'=>$data['id']);
		$tags_type  = $TagsRelationship->field('tags_type_id')->where($where)->select();
		$TagsRelationship->where($where)->delete();

		$TagsType = M('TagsType');
		foreach ($tags_type as $tt) {
			$TagsType->where('id='.$tt['tags_type_id'])->setDec('count',1);
		}
	}
	
}
?>