<?php

namespace App\ApiModule\Presenters;

class CurrenciesPresenter extends BaseApiPresenter {

	// Check if user is logged in
	public function startup()
	{
		parent::startup();
		$this->authenticate();
	}

	/**
	 * Selects all currencies from the database and shows them as JSON
	 */
	public function actionDefault() {

		$currenciesArr = [];
		$currencies = $this->orm->currencies->findAll();

		foreach ($currencies as $currency) {
			$currenciesArr[] = ['id' => $currency->id, 'symbol' => $currency->symbol];
		}

		$this->sendSuccessResponse(['currencies' => $currenciesArr]);
	}
}
