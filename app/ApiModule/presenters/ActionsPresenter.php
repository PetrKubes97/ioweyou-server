<?php

namespace App\ApiModule\Presenters;


use App\Model\Action;
use App\Model\Debt;
use Tracy\Debugger;

class ActionsPresenter extends BaseApiPresenter
{
	public function startup()
	{
		parent::startup();
		$this->authenticate();
	}

	public function actionDefault() {

		$actions = [];
		$debts = $this->user->debts;

		foreach ($debts as $debt) {

			foreach ($debt->actions as $action) {
				$actions[] = $this->convertActionToArray($action);
			}
		}

		$this->sendSuccessResponse(['actions' => $actions]);
	}

	/**
	 * Converts action entity to an array for an api response
	 * @param Action $action
	 * @return array
	 */
	private function convertActionToArray(Action $action) {

		if ($action->debt->creditor === $action->user && $action->debt->debtor != NULL) {
			$user2Id = $action->debt->debtor->id;
			$user2Name = $action->debt->debtor->name;

		} else if ($action->debt->debtor === $action->user && $action->debt->creditor != NULL) {
			$user2Id = $action->debt->creditor->id;
			$user2Name = $action->debt->creditor->name;
		} else {
			$user2Id = "";
			$user2Name = $action->debt->customFriendName;
		}

		// Convert all nulls to empty strings
		$note = (isset($action->note)) ? $action->note : "";

		return [
			'id' => $action->id,
			'type' => $action->type,
			'debtId' => $action->debt->id,
			'user1Id' => $action->user->id,
			'user1Name' => $action->user->name,
			'user2Id' => $user2Id,
			'user2Name' => $user2Name,
			'public' => (boolean) $action->public,
			'note' => $note,
			'date' => $action->date->format('Y-m-d H:i:s'),
		];
	}
}
