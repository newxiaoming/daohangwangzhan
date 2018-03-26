<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 

class FootWidget extends Widget 
{
	public function render($data)
	{
		return $this->renderFile ("index", $data);
	}
}
?>