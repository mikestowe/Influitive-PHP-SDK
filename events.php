<?php
require_once('engine.php');

class events {
	private $filters = false;
	
	static public function getAll() {
		$api = new engine();
		
		if($api->cacheExists('influitiveapi_events_all')) {
			return $api->getCache('influitiveapi_events_all');
		}
		
		if($api->get('events/')) {
			$api->addCache('influitiveapi_events_all', $api->getResponseBody(), 300);
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
	
	
	static public function getByType($type) {
		$api = new engine();
		$cache_type = str_replace(',', '', $type);
		
		if($api->cacheExists('influitiveapi_events_' . $cache_type)) {
			return $api->getCache('influitiveapi_events_' . $cache_type);
		}
		
		if($api->get('events/?types=' . $type)) {
			$api->addCache('influitiveapi_events_' . $cache_type, $api->getResponseBody(), 300);
			return $api->getResponseBody();
		}
		
		$api->throwErrorCodeException();
	}
	
	
	static public function getByMemberId($id) {
		$api = new events();
		$api->filters()->member = $id;
		return $api->returnFiltered($results);
	}
	
	public function __construct() {
		$this->filters = new stdClass();
		$this->filters->member = false;
		$this->filters->type = false;
	}
	
	public function filters() {
		return $this->filters;
	}
	
	public function returnFiltered() {
		
		if ($this->filters->type) {
			$events = self::getByType($this->filters->type);
		} else {
			$events = self::getAll();
		}
		
		if ($this->filters->member) {
			foreach ($events['events'] as $k => $result) {
				if ($result['contact_id'] != $this->filters->member) {
					unset($events['events'][$k]);
				}
			}
		}
		
		return $events;	
	}
}
