<?php

namespace App\ApiModule\Presenters;

use App;
use Nette;
use Tracy\Debugger;

class BaseApiPresenter extends Nette\Application\UI\Presenter
{
	protected $orm;
	protected $data;
	protected $request;



	public function __construct(App\Model\Orm $orm, Nette\Http\Request $request)
	{
		parent::__construct();
		$this->orm = $orm;
		$this->data = $request->getPost();
	}

	public function sendSuccessResponse($responseData, $code = 200)
	{
		$this->getHttpResponse()->setCode($code);
		$this->sendJson($responseData);
	}

	public function sendErrorResponse($message, $code = 400) {
		$this->getHttpResponse()->setCode($code);
		$this->sendJson(array('message' => $message));
	}
}
