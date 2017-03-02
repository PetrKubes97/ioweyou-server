<?php

namespace App\WebModule\Presenters;

use Nette;

class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @persistent */
	public $lang;

	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	protected function beforeRender()
	{
		$this->template->home = $this->translator->translate('front.homepage.menu.home');
		$this->template->technology = $this->translator->translate('front.homepage.menu.technology');
		$this->template->contact = $this->translator->translate('front.homepage.menu.contact');
	}

}
