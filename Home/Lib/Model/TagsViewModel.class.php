<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
class TagsViewModel extends ViewModel{
	public $viewFields = array(
		'tags'=>array('id','name','alias','_type'=>'LEFT'),
		'tags_type'=>array('id'=>'tags_type_id','type','icon','count','tags_pid'=>'pid','sort_order','title','keywords','description','_on'=>'tags_type.tags_id=tags.id','_type'=>'INNER'),
	);
}
?>