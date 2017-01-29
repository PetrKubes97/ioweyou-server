<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class DebtsMapper extends Mapper
{
	public function getActiveDebts($userId) {
		return $this->builder()->where("`paid_at` IS NULL AND `deleted_at` IS NULL AND (`creditor_id` = $userId OR `debtor_id` = $userId)");
	}
}
