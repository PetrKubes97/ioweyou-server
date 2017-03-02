<?php
namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;
/**
 * Friendship
 *
 * @property int                $id 		{primary}
 * @property User  				$user1    	{m:1 User::$lowerFriendships}
 * @property User  				$user2		{m:1 User::$higherFriendships}
 * @property DateTime			$createdAt 	{default now}
 */
class Friendship extends Entity
{

}