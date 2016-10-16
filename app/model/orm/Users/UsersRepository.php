<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class UsersRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [User::class];
	}

	public function getByFacebookId($facebookId) {
		return $this->getBy(['facebookId' => $facebookId]);
	}
}