<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class DebtsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Debt::class];
	}

}
