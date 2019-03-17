<?php
namespace My;
/**
 * author :ZXT
 * email
 */
class Zxt
{
	private $public = '';
	private $private = '';
	
	public function set_public($public = '')
	{
		$this->public = $public;
	}
	public function set_private($private = '')
	{
		$this->private = $private;
	}
	public function echo_m()
	{
		echo '<pre>';
		var_dump($this->public);
		die;
		echo '<pre>this is my diy composer pack;';
		$h = $this->get_token('token','21');
		var_dump($h);

	}
	public function rsa_encode(string $data,$public = '')
	{
		$data = str_split($data,50);

		$public = $public ?:$this->public;
		$public = openssl_pkey_get_public($public);
		if(!$public ) return false;

		$encode = '';
		foreach ($data as $key => $value) {
			$temp = '';
			openssl_public_encrypt($value,$temp,$public);
			$encode .=$temp;

		}
		openssl_free_key($public);
		return base64_encode($encode);
	}
	public function rsa_decode(string $data,$private = '')
	{
		$data = base64_decode($data);
		if(!$data) return false;


		$private = $private ?:$this->private;
		$private = openssl_pkey_get_private($private);
		if(!$private) return false;

		$res = '';
		$length = strlen($data)/128;
		for ($i=0; $i < $length; $i++) { 
			$temp = substr($data,$i * 128,128);
			$temp_decode = '';
			openssl_private_decrypt($temp,$temp_decode,$private);
			$res .= $temp_decode;

		}

		openssl_free_key($private);
		return ($res);
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
			if(substr($name,0,5)=='HTTP_'){
				$headers[$full ? $name :str_replace('','-',strtolower(str_replace('_','',substr($name,5))))] = $value;
			}
			
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
		if($method == 'head'){
			return $this->get_h($key);
		}
		if($method == 'post'){
			return isset($_POST[$key]) ? $_POST[$key] : null;
		}
		if($method == 'get'){
			return isset($_GET[$key]) ? $_GET[$key] : null;
		}
		
		return  $this->get_h($key) ?: ( isset($_POST[$key]) ? $_POST[$key] : null) ?: (isset($_GET[$key]) ? $_GET[$key] : null)?:null;
	}

	/**
	 * 跨域
	 * @param  string || array $data [允许跨域的站点]
	 */
	public function kuayu($data='')
	{
		if($data){
			if(is_string($data)){
				header('Access-Control-Allow-Origin:'.$data);
			}
			if(is_array($data)){
				foreach ($data as $value) {
					header('Access-Control-Allow-Origin:'.$value);
				}
			}
		} else {
			header('Access-Control-Allow-Origin:*');
		}
	}

	/**
	 * 获取签名sign
	 * @param  [array || string] $data [参数]
	 * @return [string]        [sign]
	 */
	public function get_sign($data, $salt = '')
	{
		if(is_array($data)){
			ksort($data);

			$str = '';
			foreach ($data as $key => $value) {
				if($key != 'sign') $str .= $key.'='.$value.'&';
			}

			return  md5(substr($str,0,-1).$salt);
		}

		if(is_string($data)) return md5($data.$salt);
		return false;

	}

    /**
     * 检查签名
     * @param  [array || string] $data [参数]
     * @param  string $sign [签名，可选，优先使用传进来的签名，不传用参数里的sign]
     * @return [boolean]       [description]
     */
    public function check_sign($data, $sign = '', $salt = '')
    {
    	$rs = $this->get_sign($data,$salt);

    	if($sign) return $rs == $sign;

    	if(isset($data['sign']) && $rs) return $rs == $data['sign'];

    	return false;
    }

}