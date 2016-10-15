<?php

namespace App\ApiModule\Presenters;

class UserPresenter extends BaseApiPresenter
{
	/** @var \App\Model\FacebookModel @inject */
	public $fb;

	public function actionMe() {
		$user = $this->orm->users->getById(1);
		$this->sendSuccessResponse(array('email'=>$user->email));
	}

	public function actionLogin() {

		if (isset($this->data['facebookId']) && isset($this->data['facebookToken'])) {

			$user = $this->fb->getUser($this->data['facebookId'], $this->data['facebookToken']);

			if ($user) {
				$this->sendSuccessResponse($user, 201);
			} else {
				$this->sendErrorResponse('Facebook token does not match facebook id', 401);
			}

		} else {
			$this->sendErrorResponse('You have to provide facebookId and facebookToken.', 400);
		}
	}
}