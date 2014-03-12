<?php

use Nette\Application\UI\Form;

/**
 * Description of UsersPresenter
 *
 * @author viky
 */
class UserPresenter extends SecuredPresenter {

    /** @var \DB\UserRepository */
    private $userRepo;

    protected function startup() {
        parent::startup();
        $this->userRepo = $this->context->userRepository;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentMcNick() {
        $mcname = $this->userRepo->findById($this->user->id)->fetch()->mcname;
        $form = new Form();
        $form->addGroup('Minecraft nick');
        $form->addText('mcname', 'Minecraft nick: ')
                ->setDefaultValue($mcname);
        $form->addSubmit('submit', 'Odeslat');
        $form->onSuccess[] = $this->mcNickSubmitted;
        return $form;
    }

    /**
     * @param Form $form
     */
    public function mcNickSubmitted($form) {
        $this->userRepo->setMcNick($this->user->id, $form->getValues()->mcname);
        $this->flashMessage("Minecraft nick změněn.", "success");
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentUserCredentials() {
        $form = new Form();
        $form->addGroup('Heslo');
        $form->addPassword('oldpass', 'Staré heslo:');
        $form->addPassword('newpass', 'Nové heslo:')
                ->addRule(Form::FILLED, 'Zadejte prosím heslo.')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
        $form->addPassword('passcheck', 'Nové heslo znovu:')
                ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['newpass'])
                ->setOmitted();
        $form->addSubmit('submit', 'Odeslat');
        $form->onSuccess[] = $this->userCredentialsSubmitted;
        return $form;
    }

    /**
     * @param Form $form
     */
    public function userCredentialsSubmitted($form) {
        $values = $form->getValues();
        $user = $this->userRepo->findById($this->user->id)->fetch();
        if (Authenticator::checkPassword($user->password, $values->oldpass)) {
            $this->userRepo->setPassword($this->user->id, $values->newpass);
            $this->flashMessage('Heslo nastaveno','success');
        } else {
            $this->flashMessage('Staré heslo bylo zadáno nesprávně', 'error');
        }
        $this->redirect('this');
    }

}
