<?php
class engine {
	
	private $baseUrl = 'https://%HUB_NAME%.influitive.com:443/api';
	private $headers = array(
		'Authorization' => 'Token %INSERT INFLUITIVE TOKEN HERE%',
		'Content-type'  => 'application/json',
		'Accept'        => 'application/json'
	);
	
	private $method = 'get';
	
	private $path = false;

	private $body = false;
	
	private $response = array(
		'body' => false,
		'code' => 'not submitted'
	);
	
	private $curlAuth = false;
	
	
	public function __construct($baseUrl = false, array $headers = null) {
		if ($baseUrl) {
			$this->baseUrl = $baseUrl;
		}
		
		if ($headers) {
			$this->headers = $headers;
		}
		
		return $this;
	}
	
	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
		return $this;
	}
	
	public function deleteHeader($name) {
		unset($this->headers[$name]);
		return $this;
	}
	
	public function getHeader($name) {
		return $this->headers[$name];
	}
	
	public function setCurlAuth($username, $password) {
		$this->curlAuth = $username . ':' . $password;
		return $this;
	}
	
	public function deleteCurlAuth() {
		$this->curlAuth = false;
		return $this;
	}
	
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	
	public function getBody() {
		return $this->body;
	}
	
	public function getResponseStatus() {
		return $this->response['code'];
	}
	
	public function getResponseBody($format = 'array') {
		if ($format == 'array' && $this->headers['Accept'] == 'application/json') {
			return json_decode($this->response['body'], true);
		}
		
		if ($format == 'object' && $this->headers['Accept'] == 'application/json') {
			return json_decode($this->response['body']);
		}

		if ($format == 'string' && is_string($this->response['body'])) {
			return $this->response['body'];
		}
		
		throw new \Exception('Unable to determine how to return response body');
	}
	
	
	public function __call($method, $arguments) {
		$this->method = strtolower($method);
		$this->path = $arguments[0];
		return $this->sendRequest();
	}
	
	protected function sendRequest() {		
		if (!$this->path) {
			throw new \Exception('No Path Provided.  Unable to make call');
		} elseif (substr($this->path, 0, 1) != '/') {
			$this->path = '/' . $this->path;
		}
		
		if (!in_array($this->method, array('get', 'post', 'put', 'patch', 'delete', 'trace', 'options', 'head'))) {
			throw new \Exception('Unknown method called');
		}
		
		$headers = array();
		foreach($this->headers as $key => $value) {
			$headers[] = $key . ': '. $value;
		}

		// CURL Request
	    $ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $this->path); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6');
		
		if ($this->curlAuth) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, $this->curlAuth);
		}
		
		if ($this->method != 'get') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));                                                                     
		}
		
		if (substr($this->method, 0, 1) == 'p') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);  
		}
		
	    $this->response['body'] = curl_exec($ch); 
		$this->response['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
		
		return (substr($this->response['code'], 0, 1) == "2");
	}

	public function throwErrorCodeException() {
		throw new \Exception('Unable to perform action, status code: ' . $this->getResponseStatus());
	}
	
	
	/* CACHE */
	public function cacheExists($key) {
		if(!function_exists('apc_fetch')) {
			return false;
		}
		
		$cache = apc_fetch($key, $success);
		return $success;
	}
	
	
	public function getCache($key) {
		if(!function_exists('apc_fetch')) {
			return false;
		}
		
		$cache = apc_fetch($key, $success);
		
		return $cache;
	}
	
	public function addCache($key, $value, $ttl) {
		if(!function_exists('apc_add')) {
			return false;
		}
		
		return apc_add($key, $value, $ttl);
	}
	
	public function deleteCache($key) {
		if(!function_exists('apc_delete')) {
			return false;
		}
		
		return apc_delete($key);
	}
	
}
