<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class UsersRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [User::class];
	}
}