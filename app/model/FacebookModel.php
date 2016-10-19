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

	public function login($id, $token) {

		try {
			$this->fb->setAccessToken($token);
			$userData = $this->fb->api('/' . $id . '?fields=email,friends');
		} catch (FacebookApiException $e) {
			return $e->getMessage();
		}

		$user = $this->orm->users->getByFacebookId($userData->id);
		// User doesn't exist, let's register him
		if (!$user) {
			$user = new User();
		}

		$user->email = $userData->email;
		$user->facebookId = $userData->id;
		$user->facebookToken = $token;

		$this->orm->users->persist($user); // it is neccessary to persist user before any friends

		foreach ($userData->friends->data as $fbFriend) {

			// go through each user's friend and add him to the database
			$friend = $this->orm->users->getByFacebookId($fbFriend->id);
			if (!$friend) {
				$friend = new User();
				$friend->registrationType = User::REGISTRATION_TYPE_AUTO;
			}
			$friend->facebookId = $fbFriend->id;
			$friend->name =$fbFriend->name;

			$friend = $this->orm->users->persistAndFlush($friend);

			// create friendship, lower id has to be user1
			if (!isset($friend->id) || $friend->id > $user->id) {
				$user1 = $user;
				$user2 = $friend;
			} else {
				$user1 = $friend;
				$user2 = $user;
			}

			$friendship = $this->orm->friendships->getByUsers($user1, $user2);

			if (!$friendship) {
				$friendship = new Friendship();
				$friendship->user1 = $user1;
				$friendship->user2 = $user2;
				$this->orm->friendships->persist($friendship);
			}
		}

		$this->orm->flush();

		return $user;
	}

}