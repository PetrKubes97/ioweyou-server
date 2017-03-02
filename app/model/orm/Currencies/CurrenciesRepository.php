<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class CurrenciesRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Currency::class];
	}

}