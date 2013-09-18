<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends SecuredPresenter {

    /** @var DB\UserRepository */
    private $userRepo;

    protected function startup() {
        parent::startup();
        $this->userRepo = $this->context->userRepository;
    }

    public function renderDefault() {
        $this->template->anyVariable = 'any value';
    }

}
