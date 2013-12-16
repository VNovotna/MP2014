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
        $this->flashMessage("Not implemented");
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentUserCredentials() {
        $form = new Form();
        $form->addGroup('Heslo');
        $form->addPassword('oldpass', 'Staré heslo:');
        $form->addPassword('newpass', 'Nové heslo:');
        $form->addPassword('passcheck', 'Nové heslo znovu:');
        $form->addSubmit('submit', 'Odeslat');
        $form->onSuccess[] = $this->userCredentialsSubmitted;
        //fill in the data
        return $form;
    }

    /**
     * @param Form $form
     */
    public function userCredentialsSubmitted($form) {
        $this->flashMessage("Not implemented");
    }

}
