<?php

namespace App\ApiModule\Presenters;

use App\Model\Action;
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
	 */
	public function actionDefault() {
		$this->sendSuccessResponse($this->getDebtsArray());
	}

	/**
	 * Updates newest debts and shows debts' most recent versions
	 */
	public function actionUpdate() {

		$allReceivedDebts = json_decode($this->request->getRawBody(), true);

		// Go through each debt saved on a mobile device right now, update the database, and return all debts
		foreach ($allReceivedDebts['debts'] as $receivedDebt) {
			$action = null;
			// Check the most important things which are nessesary for further actions
			try {
				$id = $receivedDebt['id'];
				// If id is < than 0, it means that it's a new debt and a new row must be created
				if (!isset($id) || $id < 0) {
					$debt = new Debt();

					// Add a new action
					$action = new Action();
					$action->type = Action::TYPE_DEBT_NEW;
					$action->user = $this->user;
					$action->debt = $debt;
					$action->note = 'A new debt was created.';

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

				if (!is_int($receivedDebt['version'])) {
					throw new Exception('Version has to be an int.');
				}

				// check if there are the same people being part of this debt
				if ($receivedDebt['id'] > 0 &&
					($debt->creditor->id != $receivedDebt['creditorId'] || $debt->debtor->id != $receivedDebt['debtorId']) &&
					($debt->creditor->id != $receivedDebt['debtorId'] || $debt->debtor->id != $receivedDebt['creditorId'])) {
					throw new Exception('You can not change creditor or debtor of a debt.');
				}

			} catch (Exception $e) {
				$this->sendErrorResponse('Debt ' . $id . ': ' . $e->getMessage());
			}

			if (is_bool($receivedDebt['lock'])) {
				$lock = $receivedDebt['lock'];
			} else {
				$lock = false;
			}

			// Check if the recieved debt is the most recent version
			if ($id > 0) {
				// If the recieved debt is older, send newest version
				// in case of versions being equal, debt won't be updated
				if ((int)$receivedDebt['version'] <= $debt->version) {
					continue;
				} else {
					// Check if user has the right to edit this debt
					if ($debt->manager != null && $this->user != $debt->manager) {
						continue;
					}

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



			// Create actions if user changes the debt
			if (!isset($action)) {

				if ($debt->paidAt == null && $paidAt != null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_MARK_AS_PAID, 'Debt was marked as paid. ');
				} elseif ($debt->paidAt != null && $paidAt == null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_MARK_AS_UNPAID, 'Debt was marked as unpaid. ');
				} elseif ($debt->deletedAt == null && $deletedAt != null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_DELETE, 'Debt was deleted. ');
				} elseif ($debt->deletedAt != null && $deletedAt == null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_RESTORE, 'Debt was restored. ');
				} else {
					$note = '';
					$note .= ($debt->customFriendName != $receivedDebt['customFriendName']) ? 'Name of a friend was changed. ' : '';

					if ($debt->amount != null) {
						if ($receivedDebt['thingName'] == '') {
							$note .= ($debt->amount != $receivedDebt['amount']) ? 'Amount was changed. ' : '';
							$note .= ($debt->currency->id != $receivedDebt['currencyId']) ? 'Currency was changed. ' : '';
						} else {
							$note .= 'Money were changed to a thing. ';
						}
					} else {
						if ($receivedDebt['amount'] == '') {
							$note .= ($debt->thingName != $receivedDebt['thingName']) ? 'Name of a thing was changed. ' : '';
						} else {
							$note .= 'Thing was changed to money. ';
						}
					}

					$note .= ($debt->note != $receivedDebt['note']) ? 'Note was changed. ' : '';

					if ($debt->creditor->id != $receivedDebt['creditorId']) {
						$note .= 'Creditor and debtor were switched. ';
					}

					if (($debt->manager == null && $lock == true) ||
						($debt->manager != null && $lock == false)
					) {
						$note .= 'Owner changed permissions on this debt. ';
					}

					if (!empty($note)) {
						$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_UPDATED, $note);
					}
				}
			}

			$manager = null;
			if ($lock) {
				$manager = $this->user;
			} else {
				$manager = null;
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
			$debt->manager = $manager;
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

			// Add an action for a new debt; It needs to be at the end of the script, otherwise an empty debt would be created in the database
			if (isset($action)) {
				$this->orm->actions->persistAndFlush($action);
			}
		}

		$this->sendSuccessResponse($this->getDebtsArray());
	}

	private function getDebtsArray() {

		$debts = [];

		foreach ($this->user->activeDebts as $debt) {
			$debts[] = $debt->convertToArray();
		}

		return ['debts' => $debts];
	}
}
