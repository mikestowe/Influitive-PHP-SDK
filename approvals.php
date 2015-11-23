<?php
require_once('engine.php');

class approvals {
	
	static public function getAll() {
		$api = new engine();
		
		if($api->cacheExists('influitiveapi_approvals_all')) {
			return $api->getCache('influitiveapi_approvals_all');
		}
		
		if($api->get('approvals/')) {
			$api->addCache('influitiveapi_approvals_all', $api->getResponseBody(), 180);
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
	
	
	static public function getByChallengeId($id) {
		$responses =  approvals::getAll();
			
		foreach ($responses['approvals'] as $k => $v) {
			if ($v['challenge_id'] != $id) {
				unset($responses['approvals'][$k]);
			}
		}
		
		return array_values($responses['approvals']);
	}
	
	
	static public function respond($id, $status = 'feedback_only', $feedback = false) {
		$api = new engine();
		
		$fields = array();
		if ($feedback) {
			$fields['feedback'] = $feedback;
		}
		
		$api->setBody(json_encode($fields));
		$api->post('/approvals/' . $id . '/decision/' . $status);
	}


	static public function accept($id, $feedback = false) {
		self::respond($id, 'approve', $feedback);
	}
	
	static public function reject($id, $feedback = false) {
		self::respond($id, 'reject', $feedback);
	}
	
	static public function feedback_only($id, $feedback = false) {
		self::respond($id, 'feedback_only', $feedback);
	}
}
