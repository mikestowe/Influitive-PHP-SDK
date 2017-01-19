<?php
require_once('engine.php');

class rewards {
	
	static public function getAllRedemptions() {
		$api = new engine();
		
		if($api->cacheExists('influitiveapi_redemptions_all')) {
			return $api->getCache('influitiveapi_redemptions_all');
		}
		
		if($api->get('/reward_redemptions')) {
			$api->addCache('influitiveapi_redemptions_all', $api->getResponseBody(), 180);
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
	
	
	static public function getRedemptionsByRewardId($id) {
		$responses =  rewards::getAllRedemptions();
			
		foreach ($responses['reward_redemptions'] as $k => $v) {
			if ($v['reward_id'] != $id) {
				unset($responses['reward_redemptions'][$k]);
			}
		}
		
		return array_values($responses['reward_redemptions']);
	}
	
	
	static public function respondRedemption($id, $status = 'fulfill', $feedback = false) {
		$api = new engine();
		
		$fields = array();
		if ($feedback) {
			$fields['message'] = $feedback;
		}
		
		$api->setBody(json_encode($fields));
		$api->post('/reward_redemptions/' . $id . '/decision/' . $status);
	}


	static public function fulfillRedemption($id, $feedback = false) {
		self::respondRedemption($id, 'fulfill', $feedback);
	}
	
	static public function rejectRedemption($id, $feedback = false) {
		self::respondRedemption($id, 'reject', $feedback);
	}
}
