<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * User
 *
 * @property int 						$id {primary}
 * @property string|NULL 				$apiKey
 * @property string|NULL 				$email
 * @property string|NULL 				$name
 * @property string|NULL 				$facebookId
 * @property string|NULL 				$facebookToken
 * @property string 					$registrationType	{Default self}
 * @property OneHasMany|Debt[]			$debtsToGet 		{1:m Debt::$creditor}
 * @property OneHasMany|Debt[]			$debtsToPay 		{1:m Debt::$debtor}
 * @property OneHasMany|Friendship[]  	$lowerFriendships 	{1:m Friendship::$user1}
 * @property OneHasMany|Friendship[]  	$higherFriendships 	{1:m Friendship::$user2}
 * @property DateTime					$registeredAt {default now}
 * @property-read ICollection|User[]	$friends {virtual}
 */
class User extends Entity
{
	// These types are used mainly for testing purposes and should not play a role in production
	CONST REGISTRATION_TYPE_SELF = 'self';
	CONST REGISTRATION_TYPE_AUTO = 'auto';

	protected function getterFriends() {
		$friendships = $this->getModel()->getRepository('App\Model\FriendshipsRepository')->findFriendshipsOfUser($this);

		$friends = [];
		foreach ($friendships as $friendship) {
			$friend = ($friendship->user1 === $this) ? $friendship->user2 : $friendship->user1;
			$friends[] = $friend;
		}

		return $friends;
	}
}
