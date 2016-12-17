<?php

namespace App\ApiModule\Presenters;

use App;
use App\Model\Action;
use App\Model\Debt;
use App\Model\User;
use Nette;
use Tracy\Debugger;

class BaseApiPresenter extends Nette\Application\UI\Presenter
{
	protected $orm;
	protected $data;
	protected $headers;
	protected $userModel;
	protected $request;
	protected $user;

	public function __construct(App\Model\Orm $orm, Nette\Http\Request $request, App\Model\UserModel $userModel)
	{
		parent::__construct();
		$this->orm = $orm;
		$this->userModel = $userModel;
		$this->request = $request;
		$this->data = $request->getPost();
		$this->headers = $request->getHeaders();
	}

	protected function sendSuccessResponse($responseData, $code = 200)
	{
		$this->getHttpResponse()->setCode($code);
		$this->sendJson($responseData);
	}

	protected function sendErrorResponse($message, $code = 400) {
		// Save error
		$action = $this->createAction($this->user, null, Action::TYPE_ERROR, $code . ": " . $message, false);
		$this->orm->actions->persistAndFlush($action);

		$this->getHttpResponse()->setCode($code);
		$this->sendJson(array('message' => $message));
	}

	protected function authenticate() {

		$user = null;

		if (isset($this->headers['api-key']) && strlen($this->headers['api-key'])>1) {
			$user = $this->userModel->authenticate($this->headers['api-key']);
		}

		if ($user) {
			$this->user = $user;
		} else {
			$this->sendErrorResponse('You have to be logged in to perform this action.', 401);
		}
	}

	protected function createAction($user, $debt, $type, $note = null, $public = true) {
		$action = new Action();
		$action->user = $user;
		$action->debt = $debt;
		$action->type = $type;
		$action->note = $note;
		$action->public = $public;
		return $action;
	}
}
