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

	public function getByApiKey($apiKey) {
		return $this->getBy(['apiKey' => $apiKey]);
	}

	public function getByIdAndApiKey($id, $apiKey) {
		return $this->getBy(['apiKey' => $apiKey, 'id' => $id]);
	}
}
