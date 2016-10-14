<?php

namespace App\ApiModule\Presenters;

use App;
use Nette;

class BaseApiPresenter extends Nette\Application\UI\Presenter
{
	/** @inject @var App\Model\Orm */
	public $orm;

	public function sendResponso($response)
	{
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($response, "application/json;charset=utf-8" ));
	}
}