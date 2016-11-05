<?php

namespace App\ApiModule\Presenters;

use App\Model\Debt;
use Nette\Neon\Exception;

class DebtsPresenter extends BaseApiPresenter {

	public function startup()
	{
		parent::startup();
		$this->authenticate();
	}

	/**
	 * Responses with a list of users depts
	 * @param null $which
	 */
	public function actionDefault($which = null) {

		$toPay = [];
		$toGet = [];

		foreach ($this->user->debtsToPay as $debt) {
			$toPay[] = $this->convertDebtToArray($debt);
		}

		foreach ($this->user->debtsToGet as $debt) {
			$toGet[] = $this->convertDebtToArray($debt);
		}

		if ($which == 'toPay') {
			$this->sendSuccessResponse(['debtsToPay' => $toPay]);
		} elseif ($which == 'toGet') {
			$this->sendSuccessResponse(['debtsToGet' => $toGet]);
		} else {
			$this->sendSuccessResponse(['debts' => array_merge($toPay, $toGet)]);
		}
	}

	/**
	 * Converts debt entity to an array, which is suitable for api response
	 * @param Debt $debt
	 * @return array
	 */
	private function convertDebtToArray(Debt $debt) {

		$creditorId = (isset($debt->creditor)) ? $debt->creditor->id : "";
		$debtorId = (isset($debt->debtor)) ? $debt->debtor->id : "";
		$currencyId = (isset($debt->currency)) ? $debt->currency->id : "";
		$paidAt = (isset($debt->paidAt)) ? $debt->paidAt->format('Y-m-d H:i:s') : "";
		$deletedAt = (isset($debt->deletedAt)) ? $debt->deletedAt->format('Y-m-d H:i:s') : "";

		return [
			'creditorId' => $creditorId,
			'debtorId' => $debtorId,
			'customFriendName' => $debt->customFriendName,
			'amount' => $debt->amount,
			'currencyId' => $currencyId,
			'thingName' => $debt->thingName,
			'note' => $debt->note,
			'paid_at' => $paidAt,
			'deleted_at' => $deletedAt,
			'modified_at' => $debt->modifiedAt->format('Y-m-d H:i:s')
		];
	}

	/**
	 * Adds a new debt to the database
	 */
	public function actionNew() {

		$debt = new Debt();
		$debt->creditor = $this->orm->users->getById($this->data['creditorId']);
		$debt->debtor = $this->orm->users->getById($this->data['debtorId']);
		$debt->customFriendName = $this->data['customFriendName'];
		$debt->amount = $this->data['amount'];
		$debt->currency = $this->orm->users->getById($this->data['currencyId']);
		$debt->thingName = $this->data['thingName'];
		$debt->note = $this->data['note'];

		// Check if the debt is valid
		try {
			if ($debt->creditor != $this->user && $debt->debtor != $this->user) {
				throw new Exception('Current user has to be assigned to this debt.');
			} elseif (($debt->creditor === null || $debt->debtor === null) && $debt->customFriendName == "") {
				throw new Exception('You have to set creditor and debtor, or one of them has to be customFriendName.');
			} elseif (($debt->creditor != null && $debt->debtor != null) && $debt->customFriendName != "") {
				throw new Exception('You can not set creditor, debtor and customFriendName');
			} elseif ($debt->amount != "" && ($debt->currency === null || $debt->thingName != "")) {
				throw new Exception('If amount is chosen, you have to chose currencyId and thingName must be empty');
			} elseif ($debt->thingName != "" && ($debt->amount != "" || $debt->currency != null)) {
				throw new Exception('If you chose to owe a thing, currency and amount must not be set.');
			} elseif ($debt->amount == "" && $debt->thingName == "") {
				throw new Exception('You have to set either amount or thingName');
			}
		} catch (Exception $e) {
			$this->sendErrorResponse($e->getMessage());
		}

		$debt = $this->orm->debts->persistAndFlush($debt);

		$this->sendSuccessResponse(["id" => $debt->getPersistedId()]);

	}

}
