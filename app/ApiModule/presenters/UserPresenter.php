<?php

namespace App\ApiModule\Presenters;

class UserPresenter extends BaseApiPresenter
{
	/** @var \App\Model\FacebookModel @inject */
	public $fb;

	public function actionMe() {
		$this->sendSuccessResponse(array('email'=>'test'));
	}

	public function actionLogin() {

		if (isset($this->data['facebookId']) && isset($this->data['facebookToken'])) {

			$user = $this->fb->login($this->data['facebookId'], $this->data['facebookToken']);

			// user has successfully logged in
			if ($user) {

				$this->sendSuccessResponse([
					'email' => $user->friendships->countStored()
					], 201);

			} else {
				$this->sendErrorResponse('facebookId does not match facebookToken', 401);
			}

		} else {
			$this->sendErrorResponse('You have to provide facebookId and facebookToken.', 400);
		}
	}
}