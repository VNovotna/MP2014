<?php

/**
 * You have to be loged in to see SecuredPresenter or any Presenters extending SecurePresenter
 * It also defines roles and it's permissions.
 * @author viky
 */
abstract class SecuredPresenter extends BasePresenter {

    /**
     * @var int
     * @persistent
     */
    public $selectedServerId;

    /** @var DB\ServerRepository */
    private $serverRepo;

    protected function startup() {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Na zobrazení této stránky je potřeba se přihlásit", "error");
            $this->redirect('Sign:in');
        } else {
            //set authorizator
            $this->user->setAuthorizator($this->defineACL());
            //repo
            $this->serverRepo = $this->context->serverRepository;
            //check persistent
            $this->checkServerOwner();
            $this->checkPersistent();
            $this->switchRoles($this->selectedServerId);
        }
    }

    private function checkPersistent() {
        if ($this->selectedServerId == 0) {
            $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id));
            if (count($servers) != 0) {
                $srv = $servers->fetch();
                $this->selectedServerId = $srv->id;
                $this->flashMessage("Vybrán server $srv->name ", 'info');
            }
        }
    }

    private function checkServerOwner() {
        $server = $this->serverRepo->findBy(array('user_id' => $this->user->id, 'id' => $this->selectedServerId));
        if (count($server) == 0) {
            $this->selectedServerId = 0;
        }
    }

    private function defineACL() {
        $acl = new Nette\Security\Permission;
        //role
        $acl->addRole('player');
        $acl->addRole('op', 'player');
        $acl->addRole('owner', 'op');
        $acl->addRole('admin');
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
        $acl->allow('admin');
        return $acl;
    }

    public function handleLogOut() {
        $this->user->logout();
        $this->redirect('Sign:in');
    }

    /**
     * switch selected server and user roles
     * @param int $id
     */
    public function handleSwitchServer($id) {
        $this->selectedServerId = $id;
        //switch roles
        $this->switchRoles($id);
        $this->redirect('this');
    }

    private function switchRoles($id) {
        if (isset($this->user->identity->serverRoles[$id])) {
            $newRoles = array($this->user->identity->serverRoles[$id]);
            if ($this->user->isInRole('admin')) {
                $newRoles[] = 'admin';
            }
            $this->user->getIdentity()->roles = $newRoles;
        }
    }

    public function renderDefault() {
        $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id));
        if ($this->selectedServerId != 0) {
            $srvname = $this->serverRepo->findBy(array('id' => $this->selectedServerId))->fetch();
            $this->template->activeServer = $srvname->name;
        } else {
            $this->template->activeServer = NULL;
        }
        $this->template->userServers = $servers;
    }

}