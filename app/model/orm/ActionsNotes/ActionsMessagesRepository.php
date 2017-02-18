<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

class ActionsMessagesRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [ActionMessage::class];
	}

}
