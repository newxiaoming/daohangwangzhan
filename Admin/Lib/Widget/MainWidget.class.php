<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 

class MainWidget extends Widget 
{
	public function render($data)
	{
		
		$menu = array(
			'Index'   => array(
				array('name'=>'system_info','list'=>array('index'=>'basic_info','setting'=>'system_setting','account'=>'account_setting')),
			),
			'Item'   => array(
				array('name'=>'item','list'=>array('index'=>'item_list','edit'=>'add_item')),
				array('name'=>'category_manage','list'=>array('category'=>'category_list'))
			),
			'Extend'   => array(
				array('name'=>'extend_manage','list'=>array('link'=>'friend_link','message'=>'guestbook','advert'=>lang('advert_slideshow')))
			)
		);
		
		$data['menu'] = $menu;
		$data['user'] = $_SESSION['user'];
		return $this->renderFile ("index", $data);
	}
}
?>