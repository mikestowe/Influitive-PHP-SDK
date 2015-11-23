<?php
require_once('engine.php');

class members {
	static public function getById($id) {
		$api = new engine();
		if($api->get('members/' . $id)) {
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
	
	
	static public function getByEmail($email) {
		$api = new engine();
		if($api->get('members/?email=' . urlencode($email))) {
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
}
