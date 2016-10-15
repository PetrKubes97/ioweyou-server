<?php

namespace App\Model;

use Kdyby\Facebook\Facebook;
use Kdyby\Facebook\FacebookApiException;
use Nette\Object;

class FacebookModel extends Object {

	private $fb;

	public function __construct(Facebook $facebook)
	{
		$this->fb = $facebook;
	}

	public function getUser($id, $token) {

		try {
			$this->fb->setAccessToken($token);
			return $this->fb->api('/' . $id);
		} catch (FacebookApiException $e) {
			return false;
		}

	}

}