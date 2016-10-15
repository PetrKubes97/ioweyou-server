<?php

namespace App\ApiModule\Presenters;

class UserPresenter extends BaseApiPresenter
{
	public function actionMe() {

		$user = $this->orm->users->getById(1);

		$this->sendSuccessResponse(array('email'=>$user->email));
	}

	public function actionLogin() {

		if (isset($this->data['facebookId']) && isset($this->data['facebookToken'])) {
			
			$this->sendSuccessResponse($this->data, 201);

		} else {
			$this->sendErrorResponse('You have to provide facebookId and facebookToken.', 400);
		}
	}
}