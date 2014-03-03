<?php

/**
 * Ops handler
 * @author viky
 */
class PermissionsPresenter extends SecuredPresenter {

    /** @var DB\UserRepository */
    private $userRepo;

    /** @var DB\PermissionRepository */
    private $permissionRepo;

    protected function startup() {
        parent::startup();
        $this->userRepo = $this->context->userRepository;
        $this->permissionRepo = $this->context->permissionRepository;
        if (!$this->user->isAllowed('permissions')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    public function actionDefault() {
        $lines = $this->permissionRepo->findAllOps($this->selectedServerId);
        $this->template->lines = $lines;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentNewPermForm() {
        $form = new Nette\Application\UI\Form();
        $users = $this->userRepo->findAll()->where('user.mcname NOT NULL')->fetchPairs('username', 'username');
        $form->addSelect('newOp', 'uživatel: ', $users);
        $form->addSubmit('send', 'Přidat');
        $form->onSuccess[] = $this->newPermFormSubmitted;
        return $form;
    }

    public function handleDeop($userId) {
        if ($this->permissionRepo->removeOpFromServer($this->selectedServerId, $userId, $this->runtimeHash)) {
            $this->flashMessage('Uživatel už není operátorem', 'success');
        } else {
            $this->flashMessage('Něco se pokazilo', 'error');
        }
    }

    /**
     * @param Nette\Application\UI\Form $form
     */
    public function newPermFormSubmitted($form) {
        $values = $form->getValues();
        $usId = $this->userRepo->findByName($values->newOp)->getPrimary();
        if ($usId != FALSE) {
            if ($this->permissionRepo->addOpToServer($this->selectedServerId, $usId, $this->runtimeHash)) {
                $this->flashMessage('Operátor přidán', 'success');
            } else {
                $this->flashMessage('Uživatel už je operátorem!', 'error');
            }
        } else {
            $this->flashMessage('Uživatel neexistuje.', 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

}
