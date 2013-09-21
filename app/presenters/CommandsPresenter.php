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

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
    }

    public function handleStartServer() {
        $out = $this->serverCmd->startServer('/home/viky/mcs/','minecraft_server.13w38c.jar');
        if ($out == NULL) {
            $this->flashMessage('Server started', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
        //$this->redirect('this');
    }

    public function handleStopServer() {
        $out = $this->serverCmd->stopServer();
        if ($out == NULL) {
            $this->flashMessage('Server stoped', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
        $this->redirect('this');
    }

}
