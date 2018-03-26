<?php 
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class TagsTypeModel extends Model {

	
	protected $_auto = array(
		array('create_time', 'time', 1, 'function'),
		array('update_time', 'time', 2, 'function'),
	);
	
	public function delete(){
		$id       = (int)$_GET['id'];//tags_type表的id
		$tags_id  = (int)$_GET['tags_id'];
		$TagsType = M('TagsType');
		$TagsRelationship = M('TagsRelationship');

		$TagsTypeCount = $TagsType->where('tags_pid='.$tags_id)->count();
		if($TagsTypeCount>0){
			die(json_encode(array('status'=>0,'info'=>'请先删除该分类下的子分类')));
		}
		$itemCount = $TagsRelationship->where('tags_type_id='.$id)->count();
		if($itemCount>0){
			die(json_encode(array('status'=>0,'info'=>'请先删除该分类下的项目')));
		}

		$TagsType->where('tags_id='.$tags_id)->delete();
		M('Tags')->delete($tags_id);
		die(json_encode(array('status'=>1,'info'=>'删除成功','data'=>$id)));
	}

	function _before_insert(&$data, $options){
		$Tags = M('Tags');
		$map  = array('name'=>trim($_POST['name']),'alias'=>trim($_POST['alias']));
		$tags_id = $Tags->where($map)->getField('id');
		if(empty($tags_id)){ //如果无数据则插入
			$tags_id = $Tags->add($map);
		}
		$data['tags_id'] = $tags_id;
	}
	function _after_update(&$data, $options){
		$tags = array(
			'id'    => $data['tags_id'],
			'name'  => $_POST['name'],
			'alias' => $_POST['alias'],
		);
		M('Tags')->save($tags);
		
		$tags_pid     = (int)$_POST['tags_pid'];
		$old_tags_pid = (int)$_POST['old_tags_pid'];
		if(!empty($old_tags_pid) && $tags_pid!=$old_tags_pid){
			//如果修改上级分类，则将原上级分类下的产品也更换到新的分类里
			$TagsType = M('TagsType');
			$TagsRelationship = M('TagsRelationship');
			$item = $TagsRelationship->field('item_id')->where('tags_type_id='.$data['id'])->select();
			foreach($item as $i){
				$map  = array('item_id'=>$i['item_id'],'tags_type_id'=>$old_tags_pid);
				$TagsRelationship->where($map)->setField('tags_type_id',$tags_pid);
			}

			//新分类项目总数
			$oldCount = $TagsRelationship->where('tags_type_id='.$old_tags_pid)->count();//原父分类项目总数
			$newCount = $TagsRelationship->where('tags_type_id='.$tags_pid)->count();//新父分类项目总数
			$TagsType->where('tags_id='.$old_tags_pid)->setField('count',$oldCount);
			$TagsType->where('tags_id='.$tags_pid)->setField('count',$newCount);
		}

	}


}
?>