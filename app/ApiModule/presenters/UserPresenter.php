<?php

namespace App\ApiModule\Presenters;


class UserPresenter extends BaseApiPresenter
{
	public function actionMe() {
		$this->sendResponso(array('name' => 'test', 'id' => 5));
	}
}