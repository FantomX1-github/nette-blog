<?php

namespace App\Presenters;

use Nette;

class PostPresenter extends Nette\Application\UI\Presenter
{
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Render the post.
	 * @param $postId the post id.
	 */

	public function renderShow($postId)
	{
		$post = $this->database->table('posts')->get($postId);
		if (!$post) {
			$this->error('Post not found.');
		}

		$this->template->post = $post;
	}
}