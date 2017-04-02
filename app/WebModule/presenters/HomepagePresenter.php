<?php

namespace App\WebModule\Presenters;


class HomepagePresenter extends BasePresenter
{
	public function renderDefault() {
		$this->template->intro = $this->translator->translate('front.homepage.texts.intro');
		$this->template->login = $this->translator->translate('front.homepage.texts.login');
		$this->template->overview = $this->translator->translate('front.homepage.texts.overview');
		$this->template->add = $this->translator->translate('front.homepage.texts.add');
		$this->template->actions = $this->translator->translate('front.homepage.texts.actions');
		$this->template->HhowToUse = $this->translator->translate('front.homepage.headings.howToUse');
		$this->template->Hlogin = $this->translator->translate('front.homepage.headings.login');
		$this->template->Hoverview = $this->translator->translate('front.homepage.headings.overview');
		$this->template->Hadd = $this->translator->translate('front.homepage.headings.add');
		$this->template->Hactions = $this->translator->translate('front.homepage.headings.actions');
	}

	public function renderTechnology() {

	}
}
