<?php

use Nette\Application\UI\Form;

/**
 * Sign in/up presenters.
 */
class SignPresenter extends BasePresenter {

    /** @var DB\UserRepository */
    private $userRepo;

    protected function startup() {
        parent::startup();
        $this->userRepo = $this->context->userRepository;
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new Form();
        $form->addText('username', 'Jméno:', 30, 20);
        $form->addPassword('password', 'Heslo:', 30);
        $form->addCheckbox('persistent', 'Pamatovat si mě na tomto počítači');
        $form->addSubmit('login', 'Přihlásit se');
        $form->onSuccess[] = $this->signInFormSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function signInFormSubmitted(Form $form) {
        try {
            $user = $this->getUser();
            $values = $form->getValues();
            if ($values->persistent) {
                $user->setExpiration('+30 days', FALSE);
            }
            $user->login($values->username, $values->password);
            $this->flashMessage('Přihlášení bylo úspěšné.', 'success');
            $this->redirect('Homepage:');
        } catch (\Nette\Security\AuthenticationException $e) {
            $form->addError('Neplatné uživatelské jméno nebo heslo.');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSignUpForm() {
        $form = new Form;
        $form->addText('username', 'Login:')
                ->addRule(Form::FILLED, 'Zadejte prosím uživatelské jméno.')
                ->addRule(Form::PATTERN, 'Login musí obsahovat pouze malá písmena bez diakritiky a čísla.', '[a-z_0-9]+')
                ->addRule(Form::MAX_LENGTH, 'Login může být maximálně %d znaků dlouhý', 20);
        $form->addText('mcname', 'Minecraft jméno:');
        $form['span'] = new InfoSpan('', 'Minecraft jméno nemá tvar emailu.', 'icon info');
        $form->addPassword('password', 'Heslo:')
                ->addRule(Form::FILLED, 'Zadejte prosím heslo.')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
        $form->addPassword('verify', 'Ověření hesla:')
                ->setRequired()
                ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password']);
        $form->addSubmit('send', 'Registrovat');
        $form->onSuccess[] = $this->signUpFormSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function signUpFormSubmitted(Form $form) {
        try {
            $values = $form->getValues();
            if ($this->userRepo->findByName($values->username) == FALSE) {
                $this->userRepo->addUser($values->username, $values->password, $values->mcname);
                $this->flashMessage('Uživatel zaregistrován', 'success');
                $this->redirect('Sign:in');
            } else {
                $this->flashMessage('Zvolený login již existuje', 'error');
            }
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }catch(\RuntimeException $e){
            $this->flashMessage($e->getMessage(), 'error');
        }
         $this->redirect('this');
    }

}