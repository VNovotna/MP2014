<?php

/**
 * You have to be loged in to see SecuredPresenter or any Presenters extending SecurePresenter
 *
 * @author viky
 */
abstract class SecuredPresenter extends BasePresenter {

    protected function startup() {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage("Na zobrazení této stránky je potřeba se přihlásit", "error");
            $this->redirect('Sign:in');
        }
    }

    public function handleLogOut() {
        $this->user->logout();
        $this->redirect('Sign:in');
    }

}