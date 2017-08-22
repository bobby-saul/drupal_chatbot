<?php

// update this to:
// use db to:
// 1. search for key word 
// 2. if key word found send response 

namespace Drupal\chatbot\Services;

use Drupal\Core\Database\Database;

class ChatbotService
{
	private $keywords = array();	// array of 'function' mapped by 'keywords'
	private $responses = array();	// array of 'function' arrays of 'responses'

	public function __construct(){

		$result = db_query("SELECT * FROM chatbot__keywords");
		foreach ($result as $record) {
			$this->keywords[strtolower($record->keyword)] = $record->function;
		}

		$result = db_query("SELECT DISTINCT function FROM chatbot__response");
		foreach ($result as $record) {
			$this->responses[strtolower($record->function)] = array();
			$result2 = db_query("SELECT response FROM chatbot__response where function = :record",array(':record' => $record->function));
			foreach ($result2 as $record2) {
				array_push( $this->responses[strtolower($record->function)], $record2->response );
			}
		}
	}

	public function randomResponse()
	{

		return 'outdated function';
	}

	public function getResponse($usertext) {
		$usertextArr = explode(" ", trim(strtolower($usertext)));
		foreach ($usertextArr as $userword) {
			if ( array_key_exists($userword, $this->keywords) ) {
				$key = $this->keywords[$userword];
				return $this->responses[$key][array_rand($this->responses[$key],1)];
			}
		}

		return "sorry no response found :( ";
	}
}