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
        $this->runtimeHash = $this->serverRepo->getRuntimeHash($this->selectedServerId);
    }

    public function actionDefault() {
        
    }

    public function renderDefault() {
        $this->template->running = $this->runtimeHash != NULL ? TRUE : FALSE;
        $logModel = new LogModel('/home/viky/mcs/logs/');
        $this->template->logs = $logModel->getAll();
    }

}