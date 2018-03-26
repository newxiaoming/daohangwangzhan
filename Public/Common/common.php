<?php
/*
 * 标题：设计师网址导航
 * 日期：2015-01-15
 * 作者：admin@289w.com
 * 网址：www.289w.com
 */

 
/*
 * 解析语言包
 * @params $world 一个英文单词为一个单位，多个英文单词组合成词组或句子以下划线（_）隔开
 */
function lang($world){
	$array = explode('_',$world);
	$lang  = '';
	foreach($array as $value){
		if($value)$lang .= L($value);
	}
	return $lang;
}

/*
 * 密码
 * params $password 明文密码
 */
function password($password){
	$pwd = trim($password);
	return hash_hmac('md5',md5($pwd),$pwd);
}


/*
 * 字符串截取
 * @params $str 字符串
 * @params $len 截取长度
 * @params $encoding 编码方式
 */
function mbSubstr($str,$len=25,$encoding='utf-8'){
	$str = strip_tags($str);
	$string = mb_substr($str,0,$len,$encoding);
	if(mb_strlen($str,'utf8')>$len) $string .= '...';
	return $string;
}


/*
 * 错误日志
 * @params $data 错误信息
 * @params $filename 错误信息文件保存地址
 */
function errorLog($data,$filename=''){
	if(empty($filename)) $filename = C('LOG_PATH').'Error-'.date('Ym').'.log';
	$log = array(
		'userId'   => $_SESSION['user']['id'],
		'clientIP' => get_client_ip(),
		'dateTime' => date('Y-m-d H:i:s'),
		'data'     => $data,
	);
	@file_put_contents($filename,var_export($log,true)."\n",FILE_APPEND);
}

/*
 * 生成随机码号
 * @param  $lenth 长度
 * @return $chars 字符串
 */
function randCode($length=10, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		do{$num = $chars[mt_rand(0, $max)];}while($i==0 && $num==0);
		$hash .= $num;
	}
	return $hash;
}

/*
 * 万能单条件查询函数，
 * @param $table  数据库表
 * @param $fields  字段
 * @param $id 默认以id查询
 * @param $str 以其它字段为条件查询
 */
function getFields($table,$fields,$id,$str){
	$Model = M($table);
	if(empty($str)){
		$expression='getByid';
	}else{
		$expression='getBy'.$str;
	}
	$thisaa=$Model->field($fields)->$expression($id);

	$bb=explode(',',$fields);
	if(count($bb)<=1){
		return $thisaa[$fields];
	}else{
		return $thisaa;
	}		
}
/*
 * 显示状态
 * @param $status  状态值
 * @param $type  显示类型可选:select,radio,image，如果为空则返回字符串
 * @param $data 可选状态值和显示的文字
 */
function status($status,$type='',$data='1:启用;0:禁用',$name='status'){
	if(is_array($data)){
		$data_array = $data;
	}else{
		$arr1 = explode(';',$data);
		foreach($arr1 as $a){
			$arr2 = explode(':',$a);
			$data_array[$arr2[0]] = $arr2[1];
		}
	}
	$tags = '';
	switch($type){
		case 'select':
			$tags = '';
			foreach($data_array as $k=>$v){
				$select = ctype_alnum($status)&&$k==$status?'selected="selected"':'';
				$tags .= '<option value="'.$k.'" '.$select.'>'.$v.'</option>';
			}
		break;
		case 'radio':
			$i = 0;
			foreach($data_array as $k=>$v){
				$i++;
				$checked = '';
				if((!ctype_alnum($status) && $i==1) || (ctype_alnum($status) && $k==$status)) $checked = 'checked="checked"';
				$tags .= '<input type="radio" name="'.$name.'" value="'.$k.'" '.$checked.' /><label class="ui-group-label">'.$v.'</label>';
			}
		break;
		case 'image':
			$image = $status==1?'true.png':'false.png';
			$tags  = '<img src="'.__PUBLIC__.'/Assets/img/'.$image.'" />';
		break;
		default:
			$tags = $data_array[$status];
	}
	return $tags;
}

/*
 * 系统设置
 * @param $param 数组
 */
function setting(array $param){
	extract($param);
	$output = empty($value) && !empty($default_value)?$default_value:$value;
	$readonly = $readonly==1?'readonly="readonly"':'';
	switch($tags){
		case 'text':
			$html = "<input type='{$tags}' name='{$name}' size='{$width}' class='ui-text' value='{$output}' {$readonly} />";
		break;
		case 'checkbox':
			@eval("\$options = $default_value;"); //系统选项

			//用户配置
			$config = array();
			if(!empty($value))@eval("\$config = $value;");
			if(is_array($options)){
				foreach($options as $k=>$v){
					$checked = in_array($k,$config)?"checked='checked'":"";
					$html .= "<input type='{$tags}' name='{$name}' class='input-checkbox' value='{$k}' {$checked} {$readonly} /><label class='checkbox remark'>{$v}</label>";
				}
			}else{
				$html = "Error.";
			}
		break;
		case 'radio':
			@eval("\$options = $default_value;"); //系统选项

			//用户配置
			$config = array();
			if(!empty($value))@eval("\$config = $value;");
			if(is_array($options)){
				foreach($options as $k=>$v){
					$checked = $k==$value?"checked='checked'":"";
					$html .= "<input type='{$tags}' name='{$name}' class='input-radio' value='{$k}' {$checked} /><label class='ui-group-label status-radio'>{$v}</label>";
				}
			}else{
				$html = "Error.";
			}
		break;
		case 'textarea':
			$html = "<textarea name='{$name}' class='textarea' cols='{$width}' rows='{$height}'>{$output}</textarea>";
		break;
		case 'select':
			@eval("\$options = $default_value;");
			if(is_array($options)){
				$html  = "<select name='{$name}'>";
				foreach($options as $k=>$v){
					$selected = $k==$value?"selected='selected'":"";
					$html .= "<option value='{$k}' {$selected}>{$v}</option>";
				}
				$html .= "</select>";
			}else{
				$html = "Error.";
			}
		break;
		case 'file':
			$html  = "<input type='text' size='{$width}' name='{$name}' id='{$name}' class='ui-text' style='float:left;' value='{$output}' /><input id='{$name}_file_upload' type='file' multiple='true' value='上传'>";
			if(!empty($output)) $html .= '<a id="view-'.$name.'" target="_blank" href="'.__PUBLIC__.'/Uploads/'.$output.'" style="margin-left:5px;" class="ui-button" >'.lang('view_image').'</a>';
		break;
		default:
			$html = "<input type='{$tags}' size='{$width}' name='{$name}' class='ui-text' value='{$output}' />";
		break;
	}
	return $html;
}

/**
 * @desc http请求方法
 * @param string $url 请求地址
 * @param string $method 请求方式GET|POST|DELETE
 * @param array $postfields 提交的数据,缺省空数组
 * @param array $headers 请求头,缺省空数组
 * @return string 返回信息
 */
function http( $url, $method = 'GET', array $postfields = array(), array $headers = array() )
{
    $ci = curl_init();
    /* Curl settings */
    curl_setopt( $ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
    curl_setopt( $ci, CURLOPT_CONNECTTIMEOUT, 30 );
    curl_setopt( $ci, CURLOPT_TIMEOUT, 30 );
    curl_setopt( $ci, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ci, CURLOPT_ENCODING, 'gzip' );
    curl_setopt( $ci, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ci, CURLOPT_MAXREDIRS, 5 );
    curl_setopt( $ci, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ci, CURLOPT_HEADER, false );

    switch( strtoupper( $method ) )
    {
        case 'POST':
            curl_setopt( $ci, CURLOPT_POST, true );
            if ( !empty( $postfields ) )
            {
                curl_setopt( $ci, CURLOPT_POSTFIELDS, http_build_query( $postfields ) );
            }
            break;
        case 'DELETE':
            curl_setopt( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
            if ( !empty( $postfields ) )
            {
                $url = "{$url}?" . http_build_query( $postfields );
            }
            break;
        case 'GET':
            if ( !empty( $postfields ) )
            {
                $url = "{$url}?" . http_build_query( $postfields );
            }
            break;
    }
    
    curl_setopt($ci, CURLOPT_URL, $url );
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
    curl_setopt($ci, CURLINFO_HEADER_OUT, true );
    
    $response = curl_exec( $ci );
    curl_close ($ci);
    return $response;
}


/**
  +----------------------------------------------------------
 * 把返回的数据集转换成Tree
  +----------------------------------------------------------
 * @access public
  +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
  +----------------------------------------------------------
 * @return array
  +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root=0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
		
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $list[$key]['level'] = 1;
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $list[$key]['level'] = $parent['level'] + 1;
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 *
 * 还原树形数组为普通数组
 *
 * @param type $tree
 * @param type $list
 * @param type $child
 * @return type
 */
function tree_to_list($tree, $list=array(), $child = '_child') {
    if (is_array($tree)) {
        foreach ($tree as $key => $data) {
            $item = $data;
            if (isset($data[$child])) {
                unset($item[$child]);
                $list[] = $item;
                $list = tree_to_list($data[$child], $list);
            } else {
                $list[] = $item;
            }
        }
    }
    return $list;
}
/**
 * 用于将层级列表按顺序排列
 *
 * @param type $list
 * @param type $root
 * @param type $pk
 * @param type $pid
 * @param type $child
 * @return type
 * @author jacob 2011-11-10
 */
function list_to_level($list, $root=0, $pk='id', $pid = 'pid', $child = '_child') {
    $tree = list_to_tree($list, $pk, $pid, $child, $root);
    $list = tree_to_list($tree);
    return $list;
}

//删除文件夹下所有文件
function delFiles($dir='./Public/Cache/') {
  $dh = opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          delFiles($fullpath);
      }
    }
  }
  closedir($dh);
}
//判断是否为手持设备
function isMobile(){
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}

?>