<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class AuthPresenter extends Nette\Application\UI\Presenter
{
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
		$formRenderer->wrappers['error']['item'] = 'li class="notification is-danger"';

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


	protected function createComponentSignInForm()
	{
		$form = new Form;
		$this->configureFormBulma($form);

		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = [$this, 'signInFormSuccess'];
		return $form;
	}

	public function signInFormSuccess($form, $values)
	{
		try {
			$this->getUser()->login($values->username, $values->password);
			$this->redirect('Homepage:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Invalid login Information.');
		}
	}

	/**
	 * User Signout Action Handler.
	 */

	public function actionSignOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.', 'is-primary');
		$this->redirect('Homepage:');
	}

}