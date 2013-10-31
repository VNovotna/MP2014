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

    public function actionDefault() {
        
    }

    public function renderDefault() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        try {
            $logModel = new LogModel($path . 'logs/');
            $this->template->logs = LogModel::makeColorful($logModel->getAll());
        } catch (UnexpectedValueException $e) {
            $this->template->logs = array('nic nenalezeno');
        }
    }

}