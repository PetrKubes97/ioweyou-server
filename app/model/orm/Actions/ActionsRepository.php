<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * @method ICollection|Action[] getRecentActions(User $user)
 */
class ActionsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Action::class];
	}

}
