<?php

namespace App\ApiModule\Presenters;
use Nette;

class BaseApiPresenter extends Nette\Application\UI\Presenter
{
	public function sendResponso($response)
	{
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($response, "application/json;charset=utf-8" ));
	}
}