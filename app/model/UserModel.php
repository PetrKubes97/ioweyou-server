<?php

namespace App\Model;

use Kdyby\Facebook\Facebook;
use Nette\Object;
use Nette\Utils\Random;

/**
 * Handles user's authentication and Facebook API
 */
class UserModel extends Object
{

	private $fb;
	private $orm;

	public function __construct(Facebook $facebook, Orm $orm)
	{
		$this->fb = $facebook;
		$this->orm = $orm;
	}

	public function login($fbId, $fbToken) {
		return $this->updateFromFb($fbId, $fbToken, true);
	}

	public function refresh(User $user) {
		return $this->updateFromFb($user->facebookId, $user->facebookToken);
	}

	/**
	 * Adds or updates user data in the database using the Facebook API
	 *
	 * @param $fbId
	 * @param $fbToken
	 * @param bool $generateApiKey Optionally can generate a new api key for the user
	 * @return User|mixed|\Nextras\Orm\Entity\IEntity|null
	 */
	private function updateFromFb($fbId, $fbToken, $generateApiKey = false)
	{
		// Facebook API call
		$this->fb->setAccessToken($fbToken);
		$userData = $this->fb->api('/' . $fbId . '?fields=email,friends,name');

		$user = $this->orm->users->getByFacebookId($userData->id);
		// User doesn't exist, let's register him
		if (!$user) {
			$user = new User();
			$action = $this->createAction($user, Action::TYPE_REGISTERED);
		}

		// update his data
		$user->email = $userData->email;
		$user->name = $userData->name;
		$user->facebookId = $userData->id;
		$user->facebookToken = $fbToken;

		// create a new unique api key
		if ($generateApiKey) {
			$action = $this->createAction($user, Action::TYPE_LOGGED);
			$user->apiKey = $this->generateApiKey();
		}

		$this->orm->users->persist($user); // it is neccessary to persist the user before any friends
		if (isset($action)) {
			$this->orm->actions->persist($action);
		}

		foreach ($userData->friends->data as $fbFriend) {

			// go through each user's friend and add him to the database
			$friend = $this->orm->users->getByFacebookId($fbFriend->id);
			if (!$friend) {
				$friend = new User();
				$friend->registrationType = User::REGISTRATION_TYPE_AUTO;
			}
			$friend->facebookId = $fbFriend->id;
			$friend->name = $fbFriend->name;

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

	/**
	 * Gets user by an API key
	 *
	 * @param $apiKey
	 * @return bool|mixed|\Nextras\Orm\Entity\IEntity|null
	 */
	public function authenticate($apiKey) {
		$user = $this->orm->users->getByApiKey($apiKey);
		if ($user) {
			return $user;
		} else {
			return false;
		}
	}

	/**
	 * Generates api key
	 *
	 * @return string
	 */
	private function generateApiKey() {
		$apiKey = Random::generate(63,'a-zA-Z0-9');
		if ($this->orm->users->getByApiKey($apiKey)) {
			$apiKey = $this->generateApiKey();
		}
		return $apiKey;
	}


	private function createAction(User $user, $type, $note = null) {
		$action = new Action();
		$action->user = $user;
		$action->debt = null;
		$action->type = $type;
		$action->note = $note;
		$action->public = false;
		return $action;
	}
}
