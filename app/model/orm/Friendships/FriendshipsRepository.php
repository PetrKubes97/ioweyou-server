<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * @method ICollection|Friendship[] findFriendshipsOfUser(User $user)
 */

class FriendshipsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Friendship::class];
	}

	public function getByUsers(User $user1, User $user2) {
		return ($user1 === NULL || $user2 === NULL) ? NULL : $this->getBy(['user1'=>$user1, 'user2'=>$user2]);
	}
}