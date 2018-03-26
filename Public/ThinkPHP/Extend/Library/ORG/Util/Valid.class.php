<?php

/**
 *
 * @desc 验证工具类
 * @author ak
 *
 */
class Valid
{
	/**
	 * @author ak
	 * @desc 返回验证失败信息
	 * @var array
	 */
	static protected $messages = array(
        'length'        => '长度验证失败', 
        'string'        => '字符串验证失败', 
        'regexp'        => '正则验证失败', 
        'in'            => '包含验证失败', 
        'ex'            => '排除验证失败', 
        'email'         => '邮箱地址格式错误',
        'mobile'        => '手机号格式错误',
        'ip'            => 'IP地址格式错误',
        'int'           => '数字格式错误',
        'number'        => '数字格式错误',
        'time'          => '时间格式错误',
        'array'         => '数组格式错误',
	);

	/**
	 * @author ak
	 * @desc Email地址正则表达式
	 * @var RegExp
	 */
	static protected $emailRe = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';

	/**
	 * @author ak
	 * @desc 数据验证
	 * @param array $data 要验证的数据信息
	 * @param array $validations 验证规则
	 * @example
	 * $data = array(
	 *     'field1'    => 'value1',
	 *     'field2'    => '222@qq.com',
	 *     'field3'    => '333@qq.com',
	 *     'field4'    => '444@qq.com',
	 *     'field5'    => '555@qq.com',
	 * )
	 * $validations = array(
	 *     'field1',//缺省验证类型为string
	 *     'field2'    => 'email',//验证Email地址
	 *     'field3'    => array( 'type' => 'length', 'min' => 7 ),//验证最少长度7个字符
	 *     'field4'    => array( 'email', array( 'type' => 'length', 'max' => 10 ) ),//验证Email地址,并且最大长度10个字符(多重验证)
	 *     'field5'    => array( array( 'type' => 'email' ), array( 'type' => 'length', 'min' => 7, 'max' => 10 ) ),//验证Email地址,并且最少长度7个字符,最大长度10个字符(多重验证)
	 * )
	 * list( $errors, $data ) = \UtilsValidations::validate( $data, $validations );
	 * @return array array( $errors, $data ) 返回验证通过的数据和错误信息,没有错误时$errors为空数组
	 */
	static public function validate( array $data, array $validations )
	{
		$errors = array();//错误信息
		$rdata = array();//验证通过的数据
		$messages = self::$messages;

		foreach( $validations as $field => $list )
		{
			if( is_int( $field ) )//字段容错处理
			{
				$field = is_string( $list ) ? $list : '';
				$list = 'string';
				if( empty( $field ) )
				{
					continue;
				}
			}

			is_string( $list ) && $list = array( 'type' => $list );
			!isset( $list[ 0 ] ) && $list = array( $list );
			foreach( $list as $item )
			{
				is_string( $item ) && $item = array( 'type' => $item );
				if( empty( $data[ $field ] ) && !empty( $item['blank'] ) )
				{//字段不存在或为空时可跳过验证
					isset( $item['default'] ) && $rdata[$field] = $item['default'];//默认值
					continue;
				}
				empty( $item['type'] ) && $item['type'] = 'string';
				$fun = 'type' . $item[ 'type' ];

				//验证
				if( is_null( $data[ $field ] ) || !self::$fun( $data[ $field ], $item ) )
				{//验证失败
					$errors[ $field ] = array(
                        'field'       => $field,
                        'value'       => $data[ $field ],
                        'message'     => empty( $item[ 'message' ] ) ? $messages[ $item[ 'type' ] ] : $item[ 'message' ],
					);

					isset( $item['default'] ) && $rdata[$field] = $item['default'];//默认值
				} else
				{//验证通过
					$rdata[$field] = empty( $item['untrim'] ) ? trim( $data[ $field ] ) : $data[ $field ];
				}
			}
		}
		return array( $errors, $rdata );
	}

	/**
	 * @author ak
	 * @desc 返回错误信息(组)
	 * @param string $type 错误信息类型
	 */
	static public function getMessage( $type = null )
	{
		return empty( $type ) ? self::$messages : (isset( self::$messages[$type] ) ? self::$messages[$type] : '');
	}

	/**
	 * @author ak
	 * @desc 长度验证方法
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeLength( $data, array $config = array() )
	{
		$len = strlen( $data );
		if( isset( $config[ 'min' ] ) && $len < $config[ 'min' ] )
		{
			return false;
		} else if( isset( $config[ 'max' ] ) && $len > $config[ 'max' ] )
		{
			return false;
		} else if( isset( $config[ 'length' ] ) && $len > $config[ 'length' ] )
		{
			return false;
		} else
		{
			return true;
		}
	}

	/**
	 * @author ak
	 * @desc 长度验证方法,alisa self::typeLength()
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeString( $data, array $config = array() )
	{
		return self::typeLength( $data, $config );
	}

	/**
	 * @author ak
	 * @desc 正则表达式验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeRegexp( $data, array $config )
	{
		return preg_match( $config['regexp'], $data );
	}

	/**
	 * @author ak
	 * @desc 包含验证方法,验证的数据必需包含在给定的列表中
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置,例:
	 * 	array(
	 * 	    'list' => array( ... ),//给定的列表
	 * 	    'strict' => true,//严格检查数据类型
	 *  )
	 */
	static public function typeIn( $data, array $config )
	{
		return in_array( $data, $config['list'], !empty( $config['strict'] ) );
	}

	/**
	 * @author ak
	 * @desc 排除验证方法,验证的数据必需排除在给定的列表外
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置,例:
	 * 	array(
	 * 	    'list' => array( ... ),//给定的列表
	 * 	    'strict' => true,//严格检查数据类型
	 *  )
	 */
	static public function typeEx( $data, array $config )
	{
		return !in_array( $data, $config['list'], !empty( $config['strict'] ) );
	}

	/**
	 * @author ak
	 * @desc Email地址验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeEmail( $data, array $config = array() )
	{
		$config['regexp'] = empty( $config['regexp']) ? self::$emailRe : $config['regexp'];
		return (boolean)preg_match( $config['regexp'], $data );
	}

	/**
	 * @author ak
	 * @desc Mobile地址验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeMobile( $data, array $config = array() )
	{
		$config['regexp'] = empty( $config['regexp']) ? '/^1[3-8]\d{9}$/' : $config['regexp'];
		return (boolean)preg_match( $config['regexp'], $data );
	}

	/**
	 * @author ak
	 * @desc IP地址验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeIp( $data, array $config = array() )
	{
		return $data && $data === long2ip( sprintf( '%u', ip2long( $data ) ) );
	}

	/**
	 * @author ak
	 * @desc 数字类型验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeInt( $data, array $config = array() )
	{
		if( !is_numeric( $data )
		|| (isset( $config[ 'min' ] ) && $data < $config[ 'min' ])
		|| (isset( $config[ 'max' ] ) && $data > $config[ 'max' ]) )
		{
			return false;
		} else
		{
			return true;
		}
	}

	/**
	 * @author ak
	 * @desc 数字类型验证,alisa self::typeInt()
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeNumber( $data, array $config = array() )
	{
		return self::typeInt( $data, $config );
	}

	/**
	 * @author ak
	 * @desc 时间类型验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeTime( $data, array $config = array() )
	{
		if( ($time = strtotime( $data )) <= 0
		|| (isset( $conf[ 'min' ] ) && $time < strtotime( $conf[ 'min' ] ))
		|| (isset( $conf[ 'max' ] ) && $data > strtotime( $conf[ 'max' ] )) )
		{
			return false;
		} else
		{
			return true;
		}
	}

	/**
	 * @author ak
	 * @desc 自定义方法类型验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeMethod( $data, array &$config )
	{
		//传递引用,所以用call_user_func_array()而不用call_user_func()
		call_user_func_array( $config['method'], array( $data, &$config ) );
	}

	/**
	 * @author ak
	 * @desc 数组类型验证
	 * @param mixed $data 要验证的数据
	 * @param array $config 验证配置
	 */
	static public function typeArray( $data, array $config )
	{
		return is_array( $data );
	}
}