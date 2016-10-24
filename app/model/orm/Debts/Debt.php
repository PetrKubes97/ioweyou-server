<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Debt
 *
 * @property int 				$id 		{primary}
 * @property User 				$creditor 	{m:1 User::$debtsToGet}
 * @property User 				$debtor 	{m:1 User::$debtsToPay}
 * @property Currency|NULL 		$currency 	{m:1 Currency, oneSided=true}
 * @property int|NULL			$amount
 * @property string|NULL		$note
 * @property boolean			$paid {default 0}
 * @property boolean 			$deleted {default 0}
 * @property DateTime			$createdAt {default now}
 */
class Debt extends Entity
{

}
