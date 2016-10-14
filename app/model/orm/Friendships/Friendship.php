<?php
namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;
/**
 * Friendship
 *
 * @property int                $id 		{primary}
 * @property User  				$user     	{m:1 User::$myFriendships}
 * @property User  				$friend		{m:1 User::$otherFriendships}
 * @property DateTime			$createdAt 	{default now}
 */
class Friendship extends Entity
{

}