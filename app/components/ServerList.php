<?php

/**
 *
 * @author viky
 */
class ServerList extends Nette\Application\UI\Control {

    /** @var \DB\ServerRepository */
    private $serverRepo;

    /** @var \Nette\Security\User */
    private $user;

    /** @var ServerCmd */
    private $serverCmd;

    /**
     * @param \DB\ServerRepository $serverRepository
     * @param ServerCommander $serverCommander 
     * @param \Nette\Security\User $user
     */
    public function __construct($serverRepository, $serverCommander, $user = NULL) {
        parent::__construct();
        $this->serverRepo = $serverRepository;
        $this->serverCmd = $serverCommander;
        $this->user = $user;
    }

    public function handleDelete($serverId) {
        $path = $this->serverRepo->getPath($serverId);
        try {
            $this->serverRepo->removeServer($serverId);
            BackupModel::removeDir($path);
        } catch (Exception $ex) {
            dump($ex);
        }
        $this->presenter->flashMessage("Smazáno.", 'success');
        $this->presenter->redirect('this');
    }

    public function handleStop($serverId) {
        $runtimeHash = $this->serverRepo->getRuntimeHash($serverId);
        $out = $this->serverCmd->stopServer($runtimeHash);
        if ($out == NULL) {
            $this->presenter->flashMessage('Server zastaven', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
        $this->redrawControl('list');
        $this->presenter->redrawControl('flashMessages');
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/ServerList.latte');
        if ($this->user) {
            $this->template->servers = $this->serverRepo->findBy(array('user_id' => $this->user->id));
            $this->template->userId = $this->user;
            $this->template->allowedToStop = $this->user->isAllowed('commands', 'edit');
            $this->template->allowedToDelete = $this->user->isAllowed('delete', 'edit');
        } else {
            $this->template->servers = $this->serverRepo->findAll();
            $this->template->userId = FALSE;
            $this->template->allowedToStop = TRUE;
            $this->template->allowedToDelete = TRUE;
        }
        $this->template->servers->order('id');
        $this->template->registerHelper('getVersion', '\gameUpdateModel::getVersionFromFileName');
        $this->template->render();
    }

}
