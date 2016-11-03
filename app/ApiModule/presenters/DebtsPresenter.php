<?php

namespace App\ApiModule\Presenters;

use App\Model\Debt;
use App\Model\DebtsModel;

class DebtsPresenter extends BaseApiPresenter {
	
	private $debtsModel;

	public function injectDebtsModel(DebtsModel $service)
	{
		$this->debtsModel = $service;
	}

	public function startup()
	{
		parent::startup();
		$this->authenticate();
	}

	public function actionDefault() {

	}

	public function actionNew() {

		$debt = new Debt();
		$debt->creditor = $this->orm->users->getById($this->data['creditorId']);
		$debt->debtor = $this->orm->users->getById($this->data['debtorId']);
		$debt->customFriendName = $this->data['customFriendName'];
		$debt->amount = $this->data['amount'];
		$debt->currency = $this->orm->users->getById($this->data['currencyId']);
		$debt->thingName = $this->data['thingName'];
		$debt->note = $this->data['note'];

		$this->debtsModel->newDebt($debt);

		$this->sendSuccessResponse(['asdf']);

	}

}
