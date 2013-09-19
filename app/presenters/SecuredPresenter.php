<?php

/**
 * You have to be loged in to see SecuredPresenter or any Presenters extending SecurePresenter
 * It also defines roles and it's permissions.
 * @author viky
 */
abstract class SecuredPresenter extends BasePresenter {

    protected function startup() {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Na zobrazení této stránky je potřeba se přihlásit", "error");
            $this->redirect('Sign:in');
        } else {
            $this->user->setAuthorizator($this->defineACL());
        }
    }

    private function defineACL() {
        $acl = new Nette\Security\Permission;
        //role
        $acl->addRole('player');
        $acl->addRole('op', 'player');
        $acl->addRole('owner', 'op');
        $acl->addRole('admin', 'owner');
        //resource
        $acl->addResource('status');
        $acl->addResource('commands');
        $acl->addResource('server-settings');
        $acl->addResource('system');
        //rules
        $acl->allow('player', 'status', 'view');
        $acl->allow('op', 'commands', 'edit');
        $acl->allow('op', 'server-settings', 'view');
        $acl->allow('owner', 'server-settings', 'edit');
        $acl->allow('admin', \Nette\Security\Permission::ALL, array('view', 'edit'));

        return $acl;
    }

    public function handleLogOut() {
        $this->user->logout();
        $this->redirect('Sign:in');
    }

}