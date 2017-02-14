<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class FriendshipsMapper extends Mapper
{
	/**
	 * Returns all friendship including the user
	 *
	 * @param User $user
	 * @return \Nextras\Dbal\QueryBuilder\QueryBuilder
	 */
	public function findFriendshipsOfUser(User $user)
	{
		return $this->builder()->where(sprintf('user1_id = %d OR user2_id = %d', $user->id, $user->id));
	}
}
