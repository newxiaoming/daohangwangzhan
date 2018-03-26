<?php 
/*
 * 标题：阿狸子快速订单管理系统
 * 作者：justo2008（旺旺号）
 * 官方网址：www.alizi.net
 * 淘宝店铺：https://hiweb.taobao.com/
 */
 
class ItemModel extends Model {

	protected $fields = array('id','title','url','icon','logo','description');

	public function _initialize(){
 		parent::_initialize();
 		$this->config = R('Common/config');
	}

	public function hotList(){
    		$list   = array();
    		$where = "status=1 AND logo!=''";
    		if($this->config['item_hot_show']==1 && $this->config['item_hot_num']>0){
				$list  = $this->field($this->fields)->where($where)->order('is_hot desc,sort_order desc,id DESC')->limit($this->config['item_hot_num'])->select();
    		}
    		return $list;
    }

    public function newList(){
	$list   = array();
            $list['title'] = lang('newItem');
	$where = "status=1 AND image!='' ";
	if($this->aliziConfig['item_new_show']==1 && $this->aliziConfig['item_new_num']>0){
	      $list['list']  = $this->field($this->fields)->where($where)->order('sort_order desc,id desc')->limit($this->aliziConfig['item_new_num'])->select();
	}
	return $list;
    }

    public function categoryList(string $item_category_id,$num=5 ){
    	$data   = array();
            $category = is_array($item_category_id)?$item_category_id:explode(',', $item_category_id);
            foreach($category as $cid){
                $title = M('Category')->where('id='.$cid)->getField('name');
                $list  = $this->field($this->fields)->where("status=1 AND category_id={$cid}")->order('sort_order desc,id desc')->limit($num)->select();
                $data[] = array('title'=>$title,'list'=>$list);
            }
    	return $data;
    }

}
?>