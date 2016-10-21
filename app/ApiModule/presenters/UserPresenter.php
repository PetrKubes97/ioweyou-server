<?php

namespace App\ApiModule\Presenters;

use Kdyby\Facebook\FacebookApiException;

class UserPresenter extends BaseApiPresenter
{
	/** @var \App\Model\UserModel @inject */
	public $userModel;

	public function actionMe() {
		$this->sendSuccessResponse(array('email'=>'test'));
	}

	public function actionLogin() {

		if (isset($this->data['facebookId']) && isset($this->data['facebookToken'])) {

			try {
				$user = $this->userModel->login($this->data['facebookId'], $this->data['facebookToken']);
			} catch (FacebookApiException $e) {
				$this->sendErrorResponse($e->getMessage(), 401);
			}


			// user has successfully logged in
			if ($user) {

				$friends = [];
				foreach ($user->friends as $friend) {
					$friends[] = [
						'id' => $friend->id,
						'email' => $friend->email,
						'name' => $friend->name,
						'facebookId' => $friend->facebookId
					];
				}

				$this->sendSuccessResponse([
					'id' => $user->id,
					'email' => $user->email,
					'name' => $user->name,
					'facebookId' => $user->facebookId,
					'facebookToken' => $user->facebookToken,
					'registredAt' => $user->registeredAt,
					'friends' => $friends
					], 201);

			} else {
				$this->sendErrorResponse('facebookId does not match facebookToken', 401);
			}

		} else {
			$this->sendErrorResponse('You have to provide facebookId and facebookToken.', 400);
		}
	}
}
