<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * @method ICollection|Debt[] findActiveDebts(int $id)
 * @method ICollection|Debt[] findReoccurringDebts()
 */

class DebtsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Debt::class];
	}
}
