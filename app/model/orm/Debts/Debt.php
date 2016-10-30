<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Debt
 *
 * @property int 				$id 		{primary}
 * @property User|NULL 			$creditor 	{m:1 User::$debtsToGet}
 * @property User|NULL 			$debtor 	{m:1 User::$debtsToPay}
 * @property Currency|NULL 		$currency 	{m:1 Currency, oneSided=true}
 * @property int|NULL			$amount
 * @property string|NULL		$note
 * @property boolean			$paid {default 0}
 * @property boolean 			$deleted {default 0}
 * @property DateTime			$createdAt {default now}
 * @property string|NULL		$customFriendName
 */
class Debt extends Entity
{

}
