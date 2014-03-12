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

    /** @var string */
    protected $runtimeHash;

    /** @var string server ip */
    protected $hostIp;

    protected function startup() {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage("Na zobrazení této stránky je potřeba se přihlásit", "error");
            $this->redirect('Sign:in');
        } else {
            $this->hostIp = filter_input(INPUT_SERVER, 'SERVER_ADDR');
            //set authorizator
            $this->user->setAuthorizator($this->defineACL());
            $this->serverRepo = $this->context->serverRepository;
            //check persistent
            $this->checkPersistent();
            //check if is server running
            $this->runtimeHash = $this->serverRepo->getRuntimeHash($this->selectedServerId);
            $this->isServerAlive();
        }
    }

    private function isServerAlive() {
        if ($this->runtimeHash != "" and $this->context->serverCommander->isServerRunning($this->runtimeHash) === FALSE) {
            $this->flashMessage('Server died :\'(', 'error');
            $this->runtimeHash = NULL;
            $this->serverRepo->setRuntimeHash($this->selectedServerId, '');
        }
    }

    private function checkPersistent() {
        if ($this->serverRepo->findById($this->selectedServerId)->count() == 0 ){
            $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id));
            if (count($servers) != 0) {
                $srv = $servers->fetch();
                $this->flashMessage("Vybrán server $srv->name ", 'info');
                $this->handleSwitchServer($srv->id);
            }
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
        $acl->addResource('backup');
        $acl->addResource('permissions');
        $acl->addResource('update');
        $acl->addResource('delete');
        $acl->addResource('system');
        //rules
        $acl->allow('player', 'status', 'view');
        $acl->allow('op', array('server-settings', 'permissions'), 'view');
        $acl->allow('op', array('commands', 'backup'), 'edit');
        $acl->allow('owner', array('server-settings', 'permissions', 'update', 'delete'), 'edit');
        $acl->allow('admin'); //alow everything 
        return $acl;
    }

    public function handleLogOut() {
        $this->user->logout();
        $this->redirect('Sign:in');
    }

    /**
     * switch selected server and user roles
     * @param int $id
     * @param boolean $redirect on FALSE you have to redirect manualy 
     */
    public function handleSwitchServer($id, $redirect = TRUE) {
        $this->selectedServerId = $id;
        //switch roles
        $this->switchRoles($id);
        if ($redirect) {
            $this->redirect('this');
        }
    }

    private function switchRoles($id) {
        $newRoles = $this->context->userRepository->getPermissions($this->user->id);
        if ($newRoles !== array()) {
            if ($this->user->isInRole('admin')) {
                $this->user->identity->roles = array('admin');
            } else {
                $this->user->identity->roles = array($newRoles[$id]);
            }
        }
    }

    public function beforeRender() {
        $serversIds = array_keys($this->context->userRepository->getPermissions($this->user->id));
        if ($this->selectedServerId != 0) {
            $srvname = $this->serverRepo->findBy(array('id' => $this->selectedServerId))->fetch();
            $this->template->activeServer = $srvname->name;
        } else {
            $this->template->activeServer = NULL;
        }
        $this->template->userServers = $this->serverRepo->findBy(array('id' => $serversIds));
        $this->template->running = $this->runtimeHash != NULL ? TRUE : FALSE;
    }

}
