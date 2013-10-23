<?php

/**
 * Description of CommandsPresenter
 *
 * @author viky
 */
class CommandsPresenter extends SecuredPresenter {

    /** @var ServerCommander */
    private $serverCmd;

    /** @var DB\ServerRepository */
    private $serverRepo;

    /** @var string */
    protected $runtimeHash;

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
        $this->runtimeHash = $this->serverRepo->getRuntimeHash($this->selectedServerId);
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->template->hash = $this->runtimeHash;
    }

    public function handleStartServer() {
        //check if running
        if ($this->runtimeHash != NULL) {
            $this->flashMessage('Server is already running! Or it looks to be like that. Try restart.', 'error');
        } else {
            //generte runtime hash
            $this->runtimeHash = $this->serverRepo->generateRuntimeHash();
            $this->serverRepo->setRuntimeHash($this->selectedServerId, $this->runtimeHash);
            //set correct values from settings
            $params = $this->serverRepo->getRunParams($this->selectedServerId);
            $out = $this->serverCmd->startServer($params->path, $params->executable, $this->runtimeHash);
            if ($out === array()) {
                $this->flashMessage('Server started', 'success');
            } else {
                $this->flashMessage(implode(" \n", $out), 'error');
            }
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleStopServer() {
        $out = $this->serverCmd->stopServer($this->runtimeHash);
        $this->runtimeHash = NULL;
        $this->serverRepo->setRuntimeHash($this->selectedServerId, $this->runtimeHash);
        if ($out == NULL) {
            $this->flashMessage('Server stoped', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function actionDefault() {
        if ($this->runtimeHash != "") {
            $this->redirect('Commands:started');
        }
    }

    public function actionStarted() {
        if ($this->runtimeHash == "") {
            $this->redirect('Commands:');
        }
    }

}
