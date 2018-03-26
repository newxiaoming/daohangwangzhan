<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class TagsRelationshipViewModel extends ViewModel{
	public $viewFields = array(
		'tags_relationship'=>array('item_id','_type'=>'INNER'),
		'tags_type'=>array('sort_order', '_on'=>'tags_type.id=tags_relationship.tags_type_id','_type'=>'INNER'),
		'tags'=>array('id','name','alias','_on'=>'tags_type.tags_id=tags.id','_type'=>'LEFT'),
	);
}
?>