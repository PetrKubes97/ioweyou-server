<?php

namespace App\Model;

use Kdyby\Facebook\Facebook;
use Kdyby\Facebook\FacebookApiException;
use Nette\Object;

class FacebookModel extends Object {

	private $fb;
	private $orm;

	public function __construct(Facebook $facebook, Orm $orm)
	{
		$this->fb = $facebook;
		$this->orm = $orm;
	}

	public function getUser($id, $token) {

		try {
			$this->fb->setAccessToken($token);
			$userData = $this->fb->api('/' . $id . '?fields=email,friends');
		} catch (FacebookApiException $e) {
			return $e->getMessage();
		}

		$user = $this->orm->users->getByFacebookId($userData->id);
		// Users doesn't exist, we need to register him
		if (!$user) {
			$user = new User();
			$user->email = $userData->email;
			$user->facebookId = $userData->id;
			$user->facebookToken = $token;

			$this->orm->users->persistAndFlush($user);
		}

		return $user;
	}

}