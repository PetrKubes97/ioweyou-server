<?php

namespace App\ApiModule\Presenters;


use App\Model\Action;
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


		// Convert all nulls to empty strings
		$note = (isset($action->note)) ? $action->note : "";

		return [
			'id' => $action->id,
			'type' => $action->type,
			'debtId' => $action->debt->id,
			'userId' => $action->user->id,
			'public' => (boolean) $action->public,
			'note' => $note,
			'date' => $action->date->format('Y-m-d H:i:s'),
		];
	}
}