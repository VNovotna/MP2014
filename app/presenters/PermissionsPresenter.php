<?php

/**
 * Ops handler
 * @author viky
 */
class PermissionsPresenter extends SecuredPresenter {

    /** @var DB\UserRepository @inject */
    public $userRepo;

    /** @var DB\PermissionRepository @inject */
    public $permissionRepo;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('permissions', 'view') and !$this->user->isAllowed('permissions', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }
    public function beforeRender() {
        parent::beforeRender();
        $this->permissionRepo->syncDBwithFile($this->selectedServerId);
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
        $data = $this->userRepo->findAll()->where('user.mcname NOT NULL');
        $users = array();
        foreach ($data as $user) {
            $users[$user->username] = $user->username .' ('.$user->mcname.')';
        }
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
