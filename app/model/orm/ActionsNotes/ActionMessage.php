<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;

/**
 * ActionMessage
 *
 * @property int 				$id {primary}
 * @property Action 			$action {m:1 Action::$actionMessages}
 * @property String 			$message {enum self::MESSAGE_*}
 */
class ActionMessage extends Entity
{
	const MESSAGE_DEBT_NEW = 'debt_new';
	const MESSAGE_DEBT_MARKED_AS_PAID = 'debt_marked_as_paid';
	const MESSAGE_DEBT_MARKED_AS_UNPAID = 'debt_marked_as_unpaid';
	const MESSAGE_DEBT_DELETED = 'debt_deleted';
	const MESSAGE_DEBT_RESTORED = 'debt_restored';
	const MESSAGE_DEBT_FRIEND_NAME_CHANGED = 'debt_friend_name_changed';
	const MESSAGE_DEBT_AMOUNT_CHANGED = 'debt_amount_changed';
	const MESSAGE_DEBT_CURRENCY_CHANGED = 'debt_currency_changed';
	const MESSAGE_DEBT_MONEY_TO_THING = 'debt_money_to_thing';
	const MESSAGE_DEBT_THING_TO_MONEY = 'debt_thing_to_money';
	const MESSAGE_DEBT_THING_NAME_CHANGED = 'debt_thing_name_changed';
	const MESSAGE_DEBT_NOTE_CHANGED = 'debt_note_changed';
	const MESSAGE_DEBT_CREDITOR_DEBTOR_SWITCHED = 'debt_creditor_debtor_switched';
	const MESSAGE_DEBT_PERMISSION_CHANGED = 'debt_permission_changed';
	const MESSAGE_DEBT_INTERVAL_CHANGED = 'debt_interval_changed';
}
