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
        //$this->runtimeHash = $this->serverRepo->getRuntimeHash($this->selectedServerId);
    }

    public function actionDefault() {
        
    }

    public function renderDefault() {
        $this->template->running = $this->runtimeHash != NULL ? TRUE : FALSE;
        //TODO: read logs from right folder
        $path = $this->serverRepo->getRunParams($this->selectedServerId)->path;
        try {
            $logModel = new LogModel($path . 'logs/');
            $this->template->logs = LogModel::makeColorful($logModel->getAll());
        } catch (UnexpectedValueException $e) {
            $this->template->logs = array('nic nenalezeno');
        }
    }

}