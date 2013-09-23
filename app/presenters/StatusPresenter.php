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

    /** @var string */
    private $runtimeHash;

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
        $this->runtimeHash = $this->serverRepo->getRuntimeHash($this->selectedServerId);
        if ($this->runtimeHash != "") {
            if ($this->serverCmd->isServerRunning($this->runtimeHash)) {
            } else {
                $this->flashMessage('Server died :\'(', 'error');
                $this->runtimeHash = NULL;
                $this->serverRepo->setRuntimeHash($this->selectedServerId, '');
            }
        }
    }
    public function renderDefault() {
        parent::renderDefault();
        $this->template->running = $this->runtimeHash != NULL ? TRUE : FALSE;
    }
}