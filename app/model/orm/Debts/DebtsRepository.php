<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

/**
 * @method ICollection|Debt[] getActiveDebts(int $id)
 */

class DebtsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Debt::class];
	}
}
