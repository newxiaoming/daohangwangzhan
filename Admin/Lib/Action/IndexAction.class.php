<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class IndexAction extends CommonAction {
	
	private $updateSQL = './Public/update.sql';
    public function index(){
    		$this->assign('is_update',file_exists($this->updateSQL));
		$this->display();
    }
	
	//系统设置
	public function setting(){
		if(IS_POST){
			$Model = M('Setting');
			foreach($_POST as $k=>$v){
				$v = is_array($v)?json_encode($v):$v;
				//$Model->where("name='{$k}'")->setField('value',$v);
				$Model->query("UPDATE __TABLE__ SET `value`='{$v}' WHERE `name`='{$k}' ");
				if(in_array($k, array('URL_MODEL','DEFAULT_THEME'))) $conf[$k] = $v;
			}

			$list = $Model->field('name,value')->where('status=1')->order('groups asc,sort_order asc')->select();
			foreach($list as $li) $config[$li['name']] = $li['value'];
			delFiles('./Public/Cache/');
			cache('config',$config,8640000);
			if($conf) $this->setConfig($conf);
			$this->ajaxReturn(1,lang('modify_success'),1);
		}else{
			$list  = M('Setting')->where('status =1')->order('sort_order asc')->select();
			foreach($list as $vo){ $setting[$vo['groups']][] = $vo; }
			$this->assign('setting',$setting);
			$this->display();
		}
	}

	function setConfig($config=array()){
		$configFile = getcwd().'/Public/Common/config.db.php';
		$dbfile = include($configFile);
		$dbfile = array_merge($dbfile,$config);
		$fp = fopen($configFile, "w+");
		fwrite($fp, "<?php\n return ".var_export($dbfile,true)."\n?>");
		fclose($fp);
	}
	
	//用户信息
	function account(){
		R('User/info',array('id'=>$this->uid));
		$this->display();
	}
	
	
	//魔术棒解析扩展工具
	public function __call($name,$arguments) {
		$extend = 'Extend/'.$name;
		R($extend);
		$this->display($extend);
    }

    //网站升级
    public function update(){
    		if(!file_exists($this->updateSQL)) $this->error('找不到升级文件');
    		$sql = file_get_contents($this->updateSQL);
		$sql = str_replace('cc_',C('DB_PREFIX'),$sql);
		$array_sql = preg_split("/;[\r\n]/", $sql);

		$Model = M();
		$setting = $Model->query("SELECT * FROM ".C('DB_PREFIX')."setting WHERE status=1");
		foreach($array_sql as $sql){
			$sql = trim($sql);
			if ($sql){
				if (strstr($sql, 'CREATE TABLE')){
					preg_match('/CREATE TABLE ([^ ]*)/', $sql, $matches);
					$ret = $Model->query($sql);
				} else {
					$ret = $Model->query($sql);
				}
			}
		}
		foreach($setting as $li){
			$Model->query("UPDATE ".C('DB_PREFIX')."setting SET value='".ucfirst($li['value'])."' WHERE name='{$li['name']}' ");
		}
		@unlink($this->updateSQL);
		delFiles('./Public/Cache/');
		$this->success('升级成功',U('/'));
    }
}
?>