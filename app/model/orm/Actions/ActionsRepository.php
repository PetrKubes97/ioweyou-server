<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class ActionsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Action::class];
	}

}
