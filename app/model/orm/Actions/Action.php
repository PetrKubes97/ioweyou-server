<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * Action
 *
 * @property int 				$id {primary}
 * @property string 			$type
 * @property Debt|NULL 			$debt {m:1 Debt::$actions}
 * @property User|NULL			$user {m:1 User::$actions}
 * @property string|NULL		$note
 * @property bool				$public {Default false}
 * @property DateTime			$date {Default now}
 */
class Action extends Entity
{
	const TYPE_DEBT_NEW = 'debt_new';
	const TYPE_DEBT_DELETE = 'debt_delete';
	const TYPE_DEBT_RESTORE = 'debt_restore';
	const TYPE_DEBT_MARK_AS_PAID = 'debt_mark_as_paid';
	const TYPE_DEBT_MARK_AS_UNPAID = 'debt_mark_as_unpaid';
	const TYPE_DEBT_UPDATED = 'debt_update';

	const TYPE_ERROR = 'error';
	const TYPE_REGISTERED = 'registered';
	const TYPE_LOGGED = 'logged_in';
}
