<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class FriendshipsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Friendship::class];
	}
}