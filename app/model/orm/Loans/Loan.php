<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Loan
 *
 * @property int 				$id 		{primary}
 * @property User 				$creditor 	{m:1 User::$loansToGet}
 * @property User 				$debtor 	{m:1 User::$loansToPay}
 * @property Currency|NULL 		$currency 	{m:1 Currency, oneSided=true}
 * @property int|NULL			$amount
 * @property string|NULL		$note
 * @property boolean			$paid {default 0}
 * @property boolean 			$deleted {default 0}
 * @property DateTime			$createdAt {default now}
 */
class Loan extends Entity
{

}