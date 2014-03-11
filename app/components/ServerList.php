<?php

/**
 *
 * @author viky
 */
class ServerList extends Nette\Application\UI\Control {

    /** @var \DB\ServerRepository */
    private $serverRepo;

    /** @var int */
    private $userId;
    
    /** @var ServerCmd */
    private $serverCmd;

    /**
     * @param \DB\ServerRepository $serverRepository
     * @param ServerCommander $serverCommander 
     * @param int $user_id specify when wiev for one user is needed
     */
    public function __construct($serverRepository, $serverCommander, $user_id = NULL) {
        parent::__construct();
        $this->serverRepo = $serverRepository;
        $this->serverCmd = $serverCommander;
        $this->userId = $user_id;
    }

    public function handleDelete($serverId) {
        $this->presenter->flashMessage("Not implemented");
        $this->presenter->redrawControl();
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
        if ($this->userId) {
            $this->template->servers = $this->serverRepo->findBy(array('user_id' => $this->userId));
        } else {
            $this->template->servers = $this->serverRepo->findAll();
        }
        $this->template->servers->order('id');
        $this->template->userId = $this->userId;
        $this->template->registerHelper('getVersion', '\gameUpdateModel::getVersionFromFileName');
        $this->template->render();
    }

}
