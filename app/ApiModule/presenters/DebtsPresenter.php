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
	 * Responses with a list of user's depts
	 * @param null $which
	 */
	public function actionDefault($which = null) {
		$this->sendSuccessResponse($this->getDebtsArray($which));
	}

	/**
	 * Updates newest debts and shows debts's most recent versions
	 */
	public function actionUpdate() {

		$allReceivedDebts = json_decode($this->request->getRawBody(), true);

		// Go through each debt saved on a mobile device right now, update the database, and return all debts
		foreach ($allReceivedDebts['debts'] as $receivedDebt) {


			// Check the most important things which are nessesary for further actions
			try {
				$id = $receivedDebt['id'];
				// If id is < than 0, it means that it's a new debt and a new row must be created
				if (!isset($id) || $id < 0) {
					$debt = new Debt();
				} else {
					$debt = $this->orm->debts->getById($id);
				}

				if ($debt === null) {
					throw new Exception('This debt does not exist.');
				}

				if ($receivedDebt['createdAt'] == "") {
					throw new Exception('CreatedAt has to be set.');
				}

				if ($receivedDebt['modifiedAt'] == "") {
					throw new Exception('ModifiedAt has to be set.');
				}

				if ($receivedDebt['version'] == "") {
					throw new Exception('Version has to be an int.');
				}

			} catch (Exception $e) {
				$this->sendErrorResponse('Debt ' . $id . ': ' . $e->getMessage());
			}


			// Check if the recieved debt is the most recent version
			if ($id > 0) {
				// If the recieved debt is older, send newest version
				// in case of versions being equal, debt won't be updated
				if ((int)$receivedDebt['version'] <= $debt->version) {
					continue;
				}
			}

			$paidAt = null;
			$deletedAt = null;
			if ($receivedDebt['paidAt'] != "") {
				$paidAt = DateTime::from($receivedDebt['paidAt']);
			}

			if ($receivedDebt['deletedAt'] != "") {
				$deletedAt = DateTime::from($receivedDebt['deletedAt']);
			}

			// Here we have the newest debt, let's update the databse

			$debt->creditor = $this->orm->users->getById($receivedDebt['creditorId']);
			$debt->debtor = $this->orm->users->getById($receivedDebt['debtorId']);
			$debt->customFriendName = $receivedDebt['customFriendName'];
			$debt->amount = $receivedDebt['amount'];
			$debt->currency = $this->orm->currencies->getById($receivedDebt['currencyId']);
			$debt->thingName = $receivedDebt['thingName'];
			$debt->note = $receivedDebt['note'];
			$debt->paidAt = $paidAt;
			$debt->deletedAt = $deletedAt;
			$debt->createdAt = DateTime::from($receivedDebt['createdAt']);
			$debt->modifiedAt = new DateTime();
			$debt->version = (int)$receivedDebt['version'];

			// Check if the debt is valid
			try {
				if ($debt->creditor != $this->user && $debt->debtor != $this->user) {
					throw new Exception('Current user has to be assigned to this debt.');
				} elseif (($debt->creditor === null || $debt->debtor === null) && $debt->customFriendName == "") {
					throw new Exception('You have to set creditor and debtor, or one of them has to be customFriendName. Check if the ids are valid.');
				} elseif (($debt->creditor != null && $debt->debtor != null) && $debt->customFriendName != "") {
					throw new Exception('You can not set creditor, debtor and customFriendName');
				} elseif ($debt->amount != "" && ($debt->currency === null || $debt->thingName != "")) {
					throw new Exception('If amount is chosen, you have to chose currencyId and thingName must be empty. Also, check if currency id is valid.');
				} elseif ($debt->thingName != "" && ($debt->amount != "" || $debt->currency != null)) {
					throw new Exception('If you chose to owe a thing, currency and amount must not be set.');
				} elseif ($debt->amount == "" && $debt->thingName == "") {
					throw new Exception('You have to set either amount or thingName');
				} elseif ($debt->amount == "" && $debt->thingName == "") {
					throw new Exception('You have to set either amount or thingName');
				}
			} catch (Exception $e) {
				$this->sendErrorResponse('Debt ' . $id . ': ' . $e->getMessage());
			}

			$this->orm->debts->persistAndFlush($debt);
		}

		$this->sendSuccessResponse($this->getDebtsArray());
	}

	private function getDebtsArray($which = null) {
		$toPay = [];
		$toGet = [];

		foreach ($this->user->debtsToPay as $debt) {
			$toPay[] = $this->convertDebtToArray($debt);
		}

		foreach ($this->user->debtsToGet as $debt) {
			$toGet[] = $this->convertDebtToArray($debt);
		}

		if ($which == 'toPay') {
			return ['debtsToPay' => $toPay];
		} elseif ($which == 'toGet') {
			return ['debtsToGet' => $toGet];
		} else {
			return ['debts' => array_merge($toPay, $toGet)];
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
			'createdAt' => $debt->createdAt->format('Y-m-d H:i:s'),
			'version' => $debt->version
		];
	}

}
