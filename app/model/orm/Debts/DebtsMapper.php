<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class DebtsMapper extends Mapper
{
	/**
	 * Returns user's active debts, i. e. debts, which are not deleted or paid
	 *
	 * @param $userId
	 * @return \Nextras\Dbal\QueryBuilder\QueryBuilder
	 */
	public function getActiveDebts($userId) {
		return $this->builder()->where("`paid_at` IS NULL AND `deleted_at` IS NULL AND (`creditor_id` = $userId OR `debtor_id` = $userId)");
	}
}
