<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class PostPresenter extends Nette\Application\UI\Presenter
{
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Secure the form for logged In users only.
	 */

	public function actionCreate()
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Auth:SignIn');
		}
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
		$this->template->comments = $post->related('comments')->order('created_at');

	}

	/**
	 * Configure form Appearance for Bulma.
	 * http://bulma.io/documentation/elements/form
	 * @param Form $form
	 */

	public function configureFormBulma(Form $form)
	{

		$formRenderer = $form->getRenderer();

		$formRenderer->wrappers['controls']['container'] = NULL;
		$formRenderer->wrappers['pair']['container'] = 'div class=field';
		$formRenderer->wrappers['control']['container'] = 'div class=control';
		$formRenderer->wrappers['label']['container'] = 'div class=label';

		$form->onRender[] = function ($form) {
			foreach ($form->getControls() as $control) {
				$type = $control->getOption('type');
				switch ($type) {
					case 'button':
						$control->getControlPrototype()->addClass('is-primary');
						break;
					case 'text':
						$control->getControlPrototype()->addClass('input');
						break;
					case 'textarea':
						$control->getControlPrototype()->addClass('textarea');
						break;
					default;
				}
			}
		};
	}


	/**
	 * Handle for Comment Creation Success.
	 * @param $form
	 * @param $values
	 */

	public function commentFormSuccess($form, $values)
	{
		$postId = $this->getParameter('postId');

		$this->database->table('comments')->insert([
			'post_id' => $postId,
			'name' => $values->name,
			'email' => $values->email,
			'content' => $values->content,
		]);

		$this->flashMessage('Comment Added!', 'is-success');
		$this->redirect('this');
	}

	/**
	 * Comment Form Component factory.
	 * @return Form
	 */

	protected function createComponentCommentForm()
	{
		$form = new Form;
		$this->configureFormBulma($form);

		$form->addText('name', 'Your Name')->setRequired();
		$form->addEmail('email', 'Email');
		$form->addTextArea('content', 'Comment:')
			->setRequired();

		$form->addSubmit('submit', 'Add comment');
		$form->onSuccess[] = [$this, 'commentFormSuccess'];

		return $form;

	}

	/**
	 * Post Form Component factory.
	 * @return Form
	 */

	protected function createComponentPostForm()
	{
		$form = new Form;
		$this->configureFormBulma($form);

		$form->addText('title', 'Title:')
			->setRequired();
		$form->addTextArea('content', 'Content:')
			->setRequired();

		$form->addSubmit('send', 'Publish');
		$form->onSuccess[] = [$this, 'postFormSuccess'];

		return $form;
	}

	/**
	 * Handle for Post Creation Success.
	 * @param $form
	 * @param $values
	 */

	public function postFormSuccess($form, $values)
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('You need to log in to create or edit posts.');
		}

		$postId = $this->getParameter('postId');

		if ($postId) {
			$post = $this->database->table('posts')->get($postId);
			$post->update($values);
		} else {
			$post = $this->database->table('posts')->insert($values);
		}

		$this->flashMessage("Post published!", 'is-success');
		$this->redirect('show', $post->id);
	}

	/**
	 * Action for Post Edit.
	 * @param $postId
	 */

	public function actionEdit($postId)
	{

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Auth:SignIn');
		}

		$post = $this->database->table('posts')->get($postId);
		if (!$post) {
			$this->error('Post not found');
		}
		$this['postForm']->setDefaults($post->toArray());
	}
}