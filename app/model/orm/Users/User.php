<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * User
 *
 * @property int 						$id {primary}
 * @property string 					$email
 * @property string|NULL 				$facebookId
 * @property string|NULL 				$facebookToken
 * @property OneHasMany|Loan[]			$loansToGet 		{1:m Loan::$creditor}
 * @property OneHasMany|Loan[]			$loansToPay 		{1:m Loan::$debtor}
 * @property OneHasMany|Friendship[]  	$myFriendships 		{1:m Friendship::$user}
 * @property OneHasMany|Friendship[]  	$otherFriendships 	{1:m Friendship::$friend}
 */
class User extends Entity
{

}