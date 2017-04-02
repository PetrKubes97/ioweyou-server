<?php

namespace App\ApiModule\Presenters;

use App\Model\Action;
use Kdyby\Facebook\FacebookApiException;

/**
 * Handles users
 */
class UserPresenter extends BaseApiPresenter
{

	public function actionMe() {
		$this->sendSuccessResponse(array('email'=>'test'));
	}

	/**
	 * Receives facebookId and token
	 * Uses Facebook API to fetch info about the user
	 * Shows users id and apiKey
	 */
	public function actionLogin() {

		$receivedBody = json_decode($this->request->getRawBody(), true);

		if (isset($receivedBody['facebookId']) && isset($receivedBody['facebookToken'])) {

			try {
				$user = $this->userModel->login($receivedBody['facebookId'], $receivedBody['facebookToken']);
			} catch (FacebookApiException $e) {
				$this->sendErrorResponse($e->getMessage(), 401);
			}

			// user has successfully logged in
			if ($user) {
				$this->sendSuccessResponse([
					'id' => $user->id,
					'apiKey' => $user->apiKey
				], 201);
			} else {
				$this->sendErrorResponse('facebookId does not match facebookToken', 401);
			}

		} else {
			$this->sendErrorResponse('You have to provide facebookId and facebookToken.', 400);
		}
	}

	/**
	 * Updates user using facebook API
	 * Shows all user's data as a JSON
	 */
	public function actionDefault() {
		$this->authenticate();

		// Update users info from facebook
		try {
			$user = $this->userModel->refresh($this->user);
		} catch (FacebookApiException $e) {
			$this->sendErrorResponse($e->getMessage(), 401);
		}


		$friends = [];
		foreach ($user->friends as $friend) {
			$friends[] = [
				'id' => $friend->id,
				'email' => $friend->email,
				'name' => $friend->name
			];
		}

		$this->sendSuccessResponse([
			'id' => $user->id,
			'email' => $user->email,
			'name' => $user->name,
			'registeredAt' => $user->registeredAt->format('Y-m-d H:i:s'),
			'friends' => $friends
		], 200);
	}
}
