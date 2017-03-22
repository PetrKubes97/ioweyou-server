<?php

namespace App\Model;
use Nette\Object;

class ReoccurringDebtsModel extends Object
{
	private $orm;

	public function __construct(Orm $orm)
	{
		$this->orm = $orm;
	}

    public function refresh() {

        $debts = $this->orm->debts->findReoccurringDebts();

        foreach ($debts as $debt) {
			if (($debt->intervalRunAt->getTimestamp() + $debt->intervalMinutes*60) >= date_timestamp_get(date_create())) {

				if ($debt->intervalType == Debt::INTERVAL_TYPE_ADD) {

				} else {

				}

			};
		}

    }
}
