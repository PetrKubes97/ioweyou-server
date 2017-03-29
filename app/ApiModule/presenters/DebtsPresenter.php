<?php

namespace App\ApiModule\Presenters;

use App\Model\Action;
use App\Model\ActionMessage;
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
	 * Updates newest debts and shows debts' most recent versions
	 */
	public function actionUpdate() {

		$allReceivedDebts = json_decode($this->request->getRawBody(), true);

		// Go through each debt saved on a mobile device right now, update the database, and return all debts
		foreach ($allReceivedDebts['debts'] as $receivedDebt) {
			$action = null;
			// Check the most important things which are nessesary for further actions
			try {

				if (!array_key_exists('id', $receivedDebt) ||
					!array_key_exists('createdAt', $receivedDebt) ||
					!array_key_exists('modifiedAt', $receivedDebt) ||
					!array_key_exists('version', $receivedDebt)) {
					throw new Exception('Some JSON parameters are missing.');
				}

				if (!array_key_exists('thingName', $receivedDebt) &&
					(!array_key_exists('currencyId', $receivedDebt) || !array_key_exists('amount', $receivedDebt))) {
					throw new Exception('Some JSON parameters are missing');
				}

				$id = $receivedDebt['id'];

				// If id is < than 0, it means that it's a new debt and a new row must be created
				if (!isset($id) || $id < 0) {
					$debt = new Debt();

					// Add a new action
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_NEW);

					$this->createActionMessage($action, ActionMessage::MESSAGE_DEBT_NEW);

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

				// Check if there are the same people being part of this debt
				if ($receivedDebt['id'] > 0 &&
					(
						(isset($debt->creditor->id) && $debt->creditor->id != $receivedDebt['creditorId']) ||
						(isset($debt->debtor->id) && $debt->debtor->id != $receivedDebt['debtorId'])
					) && (
						(isset($debt->creditor->id) && $debt->creditor->id != $receivedDebt['debtorId']) ||
						(isset($debt->debtor->id) && $debt->debtor->id != $receivedDebt['creditorId'])
					)
				) {
					throw new Exception('You can not change creditor or debtor of a debt.');
				}

			} catch (Exception $e) {
				if (!isset($id)) {
					$this->sendErrorResponse('Debt: ' . $e->getMessage());
				} else {
					$this->sendErrorResponse('Debt ' . $id . ': ' . $e->getMessage());
				}
			}

			if (array_key_exists('lock', $receivedDebt) && is_bool($receivedDebt['lock'])) {
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

			$customFriendName = (!empty($receivedDebt['customFriendName'])) ? $receivedDebt['customFriendName'] : null;
			$creditorId = (!empty($receivedDebt['creditorId'])) ? $receivedDebt['creditorId'] : null;
			$debtorId = (!empty($receivedDebt['debtorId'])) ? $receivedDebt['debtorId'] : null;
			$currencyId = (!empty($receivedDebt['currencyId'])) ? $receivedDebt['currencyId'] : null;

			$creditor = $this->orm->users->getById($creditorId);
			$debtor = $this->orm->users->getById($debtorId);
			$currency = $this->orm->currencies->getById($currencyId);

			$amount = (!empty($receivedDebt['amount'])) ? $receivedDebt['amount'] : null;
			$thingName = (!empty($receivedDebt['thingName'])) ? $receivedDebt['thingName'] : null;
			$note = (!empty($receivedDebt['note'])) ? $receivedDebt['note'] : null;
			$intervalMinutes =  (!empty($receivedDebt['intervalMinutes'])) ? $receivedDebt['intervalMinutes'] : null;
			$intervalType =  (!empty($receivedDebt['intervalType'])) ? $receivedDebt['intervalType'] : null;

			$manager = null;
			if ($lock) {
				$manager = $this->user;
			} else {
				$manager = null;
			}

			// Do all other validity checks
			// Check if the debt is valid
			try {
				if ($creditor != $this->user && $debtor != $this->user) {
					throw new Exception('Current user has to be assigned to this debt.');
				} elseif (($creditor === null || $debtor === null) && $customFriendName == null) {
					throw new Exception('You have to set creditor and debtor, or one of them has to be customFriendName. Check if the ids are valid.');
				} elseif (($creditor != null && $debtor != null) && $customFriendName != "") {
					throw new Exception('You can not set creditor, debtor and customFriendName');
				} elseif ($amount != "" && ($currency == null || $thingName != "")) {
					throw new Exception('If amount is chosen, you have to chose currencyId and thingName must be empty. Also, check if currency id is valid.');
				} elseif ($thingName != "" && ($amount != null || $currency != null)) {
					throw new Exception('If you chose to owe a thing, currency and amount must not be set.');
				} elseif ($amount == "" && $thingName == "") {
					throw new Exception('You have to set either amount or thingName');
				} elseif ($amount == "" && $thingName == "") {
					throw new Exception('You have to set either amount or thingName');
				} elseif (($intervalType != null && $intervalMinutes == null) ||
					($intervalType == null && $intervalMinutes != null)) {
					throw new Exception('You have to set either both intervalMinutes and intervalType or neither');
				}
			} catch (Exception $e) {
				$this->sendErrorResponse('Debt ' . $id . ': ' . $e->getMessage());
			}

			// Create actions if user changes the debt
			if (!isset($action)) {

				if ($debt->paidAt == null && $paidAt != null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_MARK_AS_PAID);
					$this->createActionMessage($action, ActionMessage::MESSAGE_DEBT_MARKED_AS_PAID);
				} elseif ($debt->paidAt != null && $paidAt == null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_MARK_AS_UNPAID);
					$this->createActionMessage($action, ActionMessage::MESSAGE_DEBT_MARKED_AS_UNPAID);
				} elseif ($debt->deletedAt == null && $deletedAt != null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_DELETE);
					$this->createActionMessage($action, ActionMessage::MESSAGE_DEBT_DELETED);
				} elseif ($debt->deletedAt != null && $deletedAt == null) {
					$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_RESTORE);
					$this->createActionMessage($action, ActionMessage::MESSAGE_DEBT_RESTORED);
				} else {
					$messages = [];
					if (empty($customFriendName) && $debt->customFriendName != $customFriendName) {
						$messages[] = ActionMessage::MESSAGE_DEBT_FRIEND_NAME_CHANGED;
					};

					if ($debt->amount != null) {
						if (empty($thingName)) {
							if ($debt->amount != $amount) {$messages[] = ActionMessage::MESSAGE_DEBT_AMOUNT_CHANGED;}
							if ($debt->currency->id != $currencyId) {$messages[] = ActionMessage::MESSAGE_DEBT_CURRENCY_CHANGED;}
						} else {
							$messages[] = ActionMessage::MESSAGE_DEBT_MONEY_TO_THING;
						}
					} else {
						if ($amount == null) {
							if ($debt->thingName != $thingName) {$messages[] = ActionMessage::MESSAGE_DEBT_THING_NAME_CHANGED;}
						} else {
							$messages[] = ActionMessage::MESSAGE_DEBT_THING_TO_MONEY;
						}
					}

					if ($debt->note != $note) {$messages[] = ActionMessage::MESSAGE_DEBT_NOTE_CHANGED;};

					if (
						(isset($debt->creditor) && $debt->creditor->id != $creditorId) ||
						(isset($debt->debtor) && $debt->debtor->id != $debtorId)
					) {
						$messages[] = ActionMessage::MESSAGE_DEBT_CREDITOR_DEBTOR_SWITCHED;
					}

					if (($debt->manager == null && $lock == true) ||
						($debt->manager != null && $lock == false)
					) {
						$messages[] = ActionMessage::MESSAGE_DEBT_PERMISSION_CHANGED;
					}

					// Set interval set at to current date
					if ($intervalMinutes != $debt->intervalMinutes || $intervalType != $debt->intervalType) {
						$intervalRunAt = new DateTime();
						$messages[] = ActionMessage::MESSAGE_DEBT_INTERVAL_CHANGED;
					}

					if (!empty($messages)) {
						$action = $this->createAction($this->user, $debt, Action::TYPE_DEBT_UPDATED, '');

						// create notes
						foreach ($messages as $message) {
							$this->createActionMessage($action, $message);
						}

					}
				}
			}

			// Here we have the newest debt, let's update the databse

			$debt->creditor = $creditor;
			$debt->debtor = $debtor;
			$debt->customFriendName = $customFriendName;
			$debt->amount = $amount;
			$debt->currency = $currency;
			$debt->thingName = $thingName;
			$debt->note = $note;
			$debt->paidAt = $paidAt;
			$debt->deletedAt = $deletedAt;
			$debt->createdAt = DateTime::from($receivedDebt['createdAt']);
			$debt->modifiedAt = new DateTime();
			$debt->manager = $manager;
			$debt->intervalRunAt = (!empty($intervalRunAt)) ? DateTime::from($intervalRunAt) : $debt->intervalRunAt;
			$debt->intervalMinutes = $intervalMinutes;
			$debt->intervalType = $intervalType;
			$debt->version = (int)$receivedDebt['version'];

			$this->orm->debts->persist($debt);

			// Add an action for a new debt; It needs to be at the end of the script, otherwise an empty debt would be created in the database
			if (isset($action)) {
				$this->orm->actions->persist($action);
			}
		}

		$this->orm->debts->flush();
		$this->orm->actions->flush();
		$this->orm->actionsMessages->flush();

		$this->sendSuccessResponse($this->getDebtsArray());
	}

	private function createActionMessage(Action $action, $message) {
		$ActionMessage = new ActionMessage();
		$ActionMessage->action = $action;
		$ActionMessage->message = $message;
		$this->orm->actionsMessages->persist($ActionMessage);
	}

	private function getDebtsArray() {

		$debts = [];

		foreach ($this->user->activeDebts as $debt) {
			$debts[] = $debt->convertToArray();
		}

		return ['debts' => $debts];
	}
}
