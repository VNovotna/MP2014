<?php

/**
 * Description of StatusPresenter
 *
 * @author viky
 */
class StatusPresenter extends SecuredPresenter {

    /** @var ServerCommander */
    private $serverCmd;

    /** @var DB\ServerRepository */
    private $serverRepo;

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
    }
    public function handleReload(){
        $this->redrawControl('log');
    }

    public function renderDefault() {
        $params = $this->serverRepo->getRunParams($this->selectedServerId);
        $path = $params['path'];
        $this->template->address = $this->hostIp.":".$params['port'];
        try {
            $logModel = new LogModel($path . 'logs/');
            $this->template->logs = LogModel::makeColorful($logModel->getAll());
        } catch (UnexpectedValueException $e) {
            $this->template->logs = array('nic nenalezeno');
        }
    }

}