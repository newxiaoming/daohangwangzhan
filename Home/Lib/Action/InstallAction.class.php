<?php
/*
 * 标题：阿狸子快速订单管理系统
 * 作者：justo2008（旺旺号）
 * 官方网址：www.alizi.net
 * 淘宝店铺：https://hiweb.taobao.com/
 */
@ini_set( "display_errors","Off" );
error_reporting(0);
class InstallAction extends Action {

	public function _initialize(){
		if(file_exists('./Public/install.lock')) $this->error('程序已经成功安装！<br>如果需要重新安装请删除：Public/install.lock');
	}
	public function index(){
		//判断目录可写
		$publicDir='./Public';

		$this->assign('publicDir',$this->iswriteable($publicDir));
		$this->display('index');
    }

    public function next(){
    		$root =  str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
    		$root = (strlen($root)==1?"":$root).'/';
    		$this->assign('root',$root);
		$this->display();
    }
    public function setup(){

    	//判断信息
		foreach($_POST['ADMIN'] as $v){
			if(trim($v)=='') $this->error("用户信息不能为空！");
		}
		if(!preg_match("/^[\w_]+_$/",$_POST['DB']['DB_PREFIX'])) $this->error("数据表前缀不符合(例如：cc_)");

		try{
			$conn=mysql_connect($_POST['DB']['DB_HOST'],$_POST['DB']['DB_USER'],$_POST['DB']['DB_PWD']);
			mysql_query("CREATE DATABASE IF NOT EXISTS {$_POST['DB']['DB_NAME']} DEFAULT CHARSET utf8 COLLATE utf8_general_ci");
			$Model = M('User',$_POST['DB']['DB_PREFIX'],"mysql://{$_POST['DB']['DB_USER']}:{$_POST['DB']['DB_PWD']}@{$_POST['DB']['DB_HOST']}:{$_POST['DB']['DB_PORT']}/{$_POST['DB']['DB_NAME']}"); 
		}catch(\Exception $e){
			$this->assign('message',$e->getMessage());
		}
		//dump($Model);exit;
		if($Model){
			
			$pre = $_POST['DB']['DB_PREFIX'];
			$sql = file_get_contents('./Public/install.sql');

			if($_POST['install_demo'])$sql .= file_get_contents('./Public/data.sql');//安装测试数据
			$sql = str_replace('cc_',$pre,$sql);
			$array_sql = preg_split("/;[\r\n]/", $sql);
			
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

			//存入管理员数据
			$admin = array(
				'username'=>trim($_POST['ADMIN']['username']),
				'password'=>password($_POST['ADMIN']['pwd']),
				'role'=>'admin',
				'create_time'=>time(),
			);
			$Model->add($admin);

			$dbfile = getcwd().'/Public/Common/config.db.php';
			if(!$this->iswriteable($dbfile)) $this->error("配置文件($dbfile)不可写。如果您使用的是Unix/Linux主机，请修改该文件的权限为777。如果您使用的是Windows主机，请联系管理员，将此文件设为everyone可写");

			//生成配置文件
			$fp = fopen($dbfile, "w+");
			fwrite($fp, "<?php\n return ".var_export($_POST['DB'],true)."\n?>");

			$lockfile=fopen('./Public/install.lock','wb');
			fclose($fp);
			$this->display('result');
		}else{
			$this->display('error');
		}

    }

	//检测目录是否可写1可写，0不可写
	function iswriteable($file){
		if(is_dir($file)){
			$dir=$file;
			if($fp = @fopen("$dir/test.txt", 'w')) {
				@fclose($fp);
				@unlink("$dir/test.txt");
				$writeable = 1;
			} else {
				$writeable = 0;
			}
		}else{
			if($fp = @fopen($file, 'a+')) {
				@fclose($fp);
				$writeable = 1;
			}else {
				$writeable = 0;
			}
		}
		return $writeable;
	}

}