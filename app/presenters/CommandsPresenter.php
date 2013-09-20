<?php

/**
 * Description of CommandsPresenter
 *
 * @author viky
 */
class CommandsPresenter extends SecuredPresenter {

    /** @var ServerCommander */
    private $serverCmd;

    public function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
    }

    public function handleStartServer() {
        $out = $this->serverCmd->startServer('/home/viky/mcs/start.sh');
        if ($out == NULL) {
            $this->flashMessage('Server started', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
    }

    public function handleStopServer() {
        $out = $this->serverCmd->stopServer();
        if ($out == NULL) {
            $this->flashMessage('Server stoped', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
    }

}
