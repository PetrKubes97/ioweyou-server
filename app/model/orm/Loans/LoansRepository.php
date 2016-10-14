<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class LoansRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Loan::class];
	}

}