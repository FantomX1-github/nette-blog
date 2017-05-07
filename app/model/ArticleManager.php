<?php

namespace App\Model;

use Nette;

class ArticleManager
{
	use Nette\SmartObject;

	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function getPublicArticles($limit = 5)
	{
		return $this->database->table('posts')
			->where('created_at < ', new \DateTime())
			->order('created_at DESC')->limit($limit);
	}
}