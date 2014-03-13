<?php

/**
 * Overwiev of servers and users for admins
 *
 * @author viky
 */
class OverwievPresenter extends SecuredPresenter {

    /** @var \DB\UserRepository @inject */
    public $userRepo;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('system')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    public function createComponentServerList() {
        return new ServerList($this->context->serverRepository, $this->context->serverCommander);
    }

    public function handleDelete($id) {
        try {
            $this->userRepo->deleteUser($id);
            $this->flashMessage('Uživatel smazán', 'success');
        } catch (\RuntimeException $ex) {
            $this->flashMessage('Chyba: ' . $ex->getMessage(), 'error');
        }
    }

    public function renderUsers() {
        $this->template->users = $this->context->userRepository->findAll()->order('id');
    }

}
