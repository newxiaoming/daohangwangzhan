<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */
 
class PublicAction extends Action {
	
	//登陆
    public function login(){

		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		if(empty($username)) $this->error('账号或密码有误');
		$Model = M('User');
		$where = array('username'=>$username ,'password'=> password($password),);
		if($userrole==2) $where['role'] = 'admin';
		$user  = $Model->where($where)->find();
		
		if(!$user) $this->error('账号或密码有误');
		if($user['status']==0) $this->error('用户状态异常，请联系管理员');
		$_SESSION['user'] = array(
			'id'         =>$user['id'],
			'role'       =>$user['role'],
			'username'   =>$user['username'],
			'login_time' =>$user['login_time'],
			'login_ip'   =>$user['login_ip'],
		);
		
		$data = array(
			'id'         => $user['id'],
			'login_ip'   => get_client_ip(),
			'login_time' => date('Y-m-d H:i:s'),
		);
		
		$Model->save($data);
		$url = explode('?url=',$_SERVER['HTTP_REFERER']);
		$url = count($url)==2?urldecode(urldecode($url[1])):"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		header('location:'.$url);
    }
	
    //退出
	public function logout(){
		$_SESSION['user'] = array();
		unset($_SESSION['user']);	
		header('location:http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
	}
	
	//清楚缓存
	public function delCache($print = true){
		delFiles('./Public/Cache/');
		delFiles('./Index/');
		if($print==true)$this->success(lang('缓存删除完成'));
	}
	public function upload(){
		import("ORG.Util.UploadFile");
		$folder = date('Ym');//用“年-月”来命名图片文件夹名称
        $upload = new UploadFile();
        $upload->maxSize   = 3292200;//设置上传文件大小
        $upload->saveRule  = uniqid; //设置上传文件规则
        $upload->savePath  = './Public/Uploads/'.$folder.'/';//设置附件上传目录
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg,bmp,txt,doc,docx,xls');//设置上传文件类型
		$upload->thumbRemoveOrigin = false; //删除原图
        $upload->thumb = false; //设置需要生成缩略图，仅对图像文件有效
        $upload->imageClassPath = 'ORG.Util.Image'; //设置引用图片类库包路径
        $upload->thumbPrefix = ''; //设置需要生成缩略图的文件前缀
        $upload->thumbSuffix = ''; //设置需要生成缩略图的文件后缀
        $upload->thumbPath = ''; //缩略图的保存路径，留空的话取文件上传目录本身
        $upload->subType = 'date'; //子目录创建方式，默认为hash，可以设置为hash或者date
        $upload->thumbMaxWidth = '200';//设置缩略图最大宽度
        $upload->thumbMaxHeight = '200'; //设置缩略图最大高度
		
        if(!$upload->upload()) {
			exit($upload->getErrorMsg());
        }else{
            $uploadList = $upload->getUploadFileInfo();//取得成功上传的文件信息
            foreach($uploadList as $k=>$v){
				$_POST['image'][$k] = '/'.$folder.'/'.$v['savename'];
			}
			echo $_POST['image'][0];
        }
	}

	//生成html
	public function buildHtml(){
		$dir = getcwd().'/Index/';
		$flag = $this->createFolders($dir);
	
		if($flag==false || $this->iswriteable($dir)==false){
			$this->error($dir."目录不可写，请手动创建Index目录，并赋于写入权限");
		}
		$conf['URL_MODEL']=2;
		$conf['HTML_CACHE_ON']=true;
		R('Index/setConfig',array('config'=>$conf));
		//生成静态需要开启配置HTML_CACHE_ON=true
		$list = M('Tags')->field('alias')->select();
		array_push($list,array('alias'=>'message'));
		foreach($list as $li){
			$url = "http://{$_SERVER['HTTP_HOST']}".C('ROOT_FILE')."index.php?m=Index&a={$li['alias']}";
			http($url);
		}
		$conf['HTML_CACHE_ON']=false;
		//R('Index/setConfig',array('config'=>$conf));
		M('Setting')->where("name='url_model'")->setField("value=2");
		$this->success("静态生成完成！");
	}

	function createFolders($dir){
	    return is_dir($dir) or ($this->createFolders(dirname($dir)) and mkdir($dir, 0777));
	}

	function iswriteable($file){
		if(!file_exists($file))  @mkdir('$file',0777);  
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
?>