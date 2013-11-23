<?php

/**
 * 
 * @author viky
 */
class PermissionsPresenter extends SecuredPresenter {

    /** @var DB\UserRepository */
    private $userRepo;

    protected function startup() {
        parent::startup();
        $this->userRepo = $this->context->userRepository;
    }

    public function actionDefault() {
        $lines = $this->userRepo->findAllFromServer($this->selectedServerId);
        $this->template->lines = $lines;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentNewPermForm() {
        $form = new Nette\Application\UI\Form();
        $users = $this->userRepo->findAll()->fetchPairs('id', 'username');
        $form->addSelect('newOp', 'uživatel: ', $users);
        $form->addSubmit('send', 'Přidat');
        $form->onSuccess[] = $this->newPermFormSubmitted;
        return $form;
    }

    /**
     * @param Nette\Application\UI\Form $form
     */
    public function newPermFormSubmitted($form) {
        $values = $form->getValues();
        $this->flashMessage('Not implemented yet.');
    }

}
