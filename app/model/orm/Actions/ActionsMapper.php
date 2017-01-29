<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class ActionsMapper extends Mapper
{
	public function getRecentActions(User $user) {
		$id = $user->id;
		return $this->connection->query("SELECT actions.* FROM actions INNER JOIN debts ON actions.debt_id = debts.id WHERE debts.creditor_id = $id OR debts.debtor_id = $id  ORDER BY actions.date DESC LIMIT 10");
	}
}
