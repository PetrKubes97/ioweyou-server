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
 * @property string|NULL		$customFriendName
 * @property string|NULL		$thingName
 * @property int|NULL			$amount
 * @property string|NULL		$note
 * @property DateTime|NULL		$paidAt {default null}
 * @property DateTime|NULL 		$deletedAt {default null}
 * @property DateTime			$createdAt {default now}
 * @property DateTime			$modifiedAt {default now}
 */
class Debt extends Entity
{

}
