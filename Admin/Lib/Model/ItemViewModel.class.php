<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class ItemViewModel extends ViewModel{
	public $viewFields = array(
		'item'=>array('*', '_type'=>'INNER'),
		'tags_relationship'=>array('tags_type_id','_on'=>'tags_relationship.item_id=item.id','_type'=>'LEFT'),
	);
}
?>