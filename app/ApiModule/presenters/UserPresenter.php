<?php

namespace App\ApiModule\Presenters;

class UserPresenter extends BaseApiPresenter
{
	public function actionMe() {

		$user = $this->orm->users->getById(1);

		$this->sendResponso(array('name' => $user->email));
	}
}