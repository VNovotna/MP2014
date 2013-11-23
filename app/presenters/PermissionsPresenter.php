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

}
