<?php
namespace My;
/**
 * author :ZXT
 * email
 */
class Zxt
{
	
	public function echo_m()
	{
		echo '<pre>this is my diy composer pack;';
		$h = $this->get_token('token','21');
		var_dump($h);

	}

	/**
	 * get_header
	 * 获取header 
	 * @param  [string]  $key  [要获取的header，默认null，返回所有header]
	 * @param  boolean $full [head写全名，默认不用写 "HTTP_" 前缀]
	 * @return  token值
	 */
	public function get_h($key = null,$full = false)
	{
		foreach ($_SERVER as $name=>$value)
		{
			if(substr($name,0,5)=='HTTP_') $headers[$full ? $name :str_replace('','-',strtolower(str_replace('_','',substr($name,5))))] = $value;
			//var_dump(str_replace('','-',ucwords(strtolower(str_replace('_','',substr($name,5))))));
		}
		if($key !== null){
			return $key != '' && isset($headers[strtolower($key)]) ? $headers[strtolower($key)] : null;
		}
		return $headers;
	}

	/**
	 * 获取token 
	 * @param  string $key    [token名，默认token]
	 * @param  string $method [获取token的方法，默认从head获取，支持get post head]
	 * @return  token值
	 */
	public function get_token($key = 'token',$method = 'head')
	{
		if($method == 'post'){
			return isset($_POST[$key]) ? $_POST[$key] : null;
		}
		if($method == 'get'){
			return isset($_GET[$key]) ? $_GET[$key] : null;
		}
		if($method == 'head'){
			return $this->get_h($key);
		}
		
		return  $this->get_h($key) ?: ( isset($_POST[$key]) ? $_POST[$key] : null) ?: (isset($_GET[$key]) ? $_GET[$key] : null)?:null;
	}
}