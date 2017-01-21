<?php

namespace App\WebModule\Presenters;

use App\Model\Orm;
use Nette;

class HomepagePresenter extends Nette\Application\UI\Presenter
{
	private $orm;

	public function __construct(Orm $orm)
	{
		$this->orm = $orm;
	}

	public function renderDefault() {

	}

}
