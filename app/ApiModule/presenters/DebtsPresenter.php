<?php

namespace App\ApiModule\Presenters;

use App\Model\Debt;
use Nette\Neon\Exception;
use Nette\Utils\DateTime;
use Tracy;

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
	 * Updates newest debts and shows debt's most recent version
	 */
	public function actionUpdate() {

		$id = $this->data['id'];

		// Check the most important things which are nessesary for further actions
		try {
			// If id is < than 0, it means that it's a new debt and a new row must be created
			if (!isset($id) || $id < 0) {
				$debt = new Debt();
			} else {
				$debt = $this->orm->debts->getById($id);
			}

			if ($debt === null) {
				throw new Exception('This debt does not exist.');
			}

			if ($this->data['createdAt'] == "") {
				throw new Exception('CreatedAt has to be set.');
			}

			if ($this->data['modifiedAt'] == "") {
				throw new Exception('ModifiedAt has to be set.');
			}


		} catch (Exception $e) {
			$this->sendErrorResponse($e->getMessage());
		}


		// Check if the recieved debt is the most recent version
		if ($id > 0) {
			$modifiedAt = DateTime::from($this->data['modifiedAt']);

			// If the recieved debt is older, send newest version
			if ($modifiedAt < $debt->modifiedAt) {
				$this->sendDebtSuccessResponse($debt);
			}
		}

		$paidAt = null;
		$deletedAt = null;
		if ($this->data['paidAt'] != "") {
			$paidAt = DateTime::from($this->data['paidAt']);
		}

		if ($this->data['deletedAt'] != "") {
			$deletedAt = DateTime::from($this->data['deletedAt']);
		}


		// Here we have the newest debt, let's update the databse

		$debt->creditor = $this->orm->users->getById($this->data['creditorId']);
		$debt->debtor = $this->orm->users->getById($this->data['debtorId']);
		$debt->customFriendName = $this->data['customFriendName'];
		$debt->amount = $this->data['amount'];
		$debt->currency = $this->orm->users->getById($this->data['currencyId']);
		$debt->thingName = $this->data['thingName'];
		$debt->note = $this->data['note'];
		$debt->paidAt = $paidAt;
		$debt->deletedAt = $deletedAt;
		$debt->createdAt = DateTime::from($this->data['createdAt']);
		$debt->modifiedAt = new DateTime();

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
			} elseif ($debt->amount == "" && $debt->thingName == "") {
				throw new Exception('You have to set either amount or thingName');
			}
		} catch (Exception $e) {
			$this->sendErrorResponse($e->getMessage());
		}

		$debt = $this->orm->debts->persistAndFlush($debt);

		$this->sendDebtSuccessResponse($debt);
	}

	private function sendDebtSuccessResponse(Debt $debt) {
		$this->sendSuccessResponse($this->convertDebtToArray($debt));
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
			'id' => $debt->id,
			'creditorId' => $creditorId,
			'debtorId' => $debtorId,
			'customFriendName' => $debt->customFriendName,
			'amount' => $debt->amount,
			'currencyId' => $currencyId,
			'thingName' => $debt->thingName,
			'note' => $debt->note,
			'paidAt' => $paidAt,
			'deletedAt' => $deletedAt,
			'modifiedAt' => $debt->modifiedAt->format('Y-m-d H:i:s'),
			'createdAt' => $debt->createdAt->format('Y-m-d H:i:s')
		];
	}

}
