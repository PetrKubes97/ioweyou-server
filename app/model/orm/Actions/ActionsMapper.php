<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class ActionsMapper extends Mapper
{
	/**
	 * Selects recent public actions, which are connected to user's debts
	 *
	 * @param User $user
	 * @return \Nextras\Dbal\Result\Result|NULL
	 */
	public function getRecentActions(User $user) {
		$id = $user->id;
		return $this->connection->query("SELECT actions.* FROM actions INNER JOIN debts ON actions.debt_id = debts.id WHERE (debts.creditor_id = $id OR debts.debtor_id = $id) AND actions.public = TRUE ORDER BY actions.date DESC LIMIT 10");
	}
}
