<?php

namespace App\Presenters;

use App\Model\ArticleManager;
use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	private $articleManager;

	public function __construct(ArticleManager $articleManager)
	{
		$this->articleManager = $articleManager;
	}

	public function renderDefault()
	{
		$this->template->posts = $this->articleManager->getPublicArticles(5);
	}

}