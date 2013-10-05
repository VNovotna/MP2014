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

    public function renderDefault() {
        $this->template->running = $this->runtimeHash != NULL ? TRUE : FALSE;
        $pokus = new LogModel('/home/viky/mcs/');
        dump($pokus->getAll());
    }

}