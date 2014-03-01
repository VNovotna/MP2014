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

    /**
     * @param \DB\ServerRepository $serverRepository
     * @param int $user_id specify when wiev for one user is needed
     */
    public function __construct($serverRepository, $user_id = NULL) {
        parent::__construct();
        $this->serverRepo = $serverRepository;
        $this->userId = $user_id;
    }
    public function handleDelete($serverId){
        $this->getPresenter()->flashMessage("Not implemented");
        $this->getPresenter()->redrawControl();
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/ServerList.latte');
        if($this->userId){
            $this->template->servers = $this->serverRepo->findBy(array('user_id' => $this->userId));
        }else{
            $this->template->servers = $this->serverRepo->findAll();
        }
        $this->template->servers->order('id');
        $this->template->userId = $this->userId;
        $this->template->registerHelper('getVersion', '\gameUpdateModel::getVersionFromFileName');
        $this->template->render();
    }

}
