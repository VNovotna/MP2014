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

    public function beforeRender() {
        parent::beforeRender();
        $this->template->hash = $this->runtimeHash;
    }

    public function handleStartServer() {
        //check if running
        if ($this->runtimeHash != NULL) {
            $this->flashMessage('Server is already running! Or it looks to be like that. :/', 'error');
        } else {
            //generte runtime hash
            $this->runtimeHash = $this->serverRepo->generateRuntimeHash();
            $this->serverRepo->setRuntimeHash($this->selectedServerId, $this->runtimeHash);
            //set correct values from settings
            $params = $this->serverRepo->getRunParams($this->selectedServerId);
            $out = $this->serverCmd->startServer($params->path, $params->executable, $this->runtimeHash);
            if ($out === array()) {
                $this->flashMessage('Server naběhl. Adresa pro připojení: '.$this->hostIp.':'.$params->port, 'success');
            } else {
                $this->flashMessage(implode(" \n", $out), 'error');
            }
        }
        $this->redirect('this');
    }

    public function handleStopServer() {
        $out = $this->serverCmd->stopServer($this->runtimeHash);
        $this->runtimeHash = NULL;
        if ($out == NULL) {
            $this->flashMessage('Server stoped', 'success');
        } else {
            $this->flashMessage(implode(" \n", $out), 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentCommandForm() {
        $form = new Nette\Application\UI\Form();
        $form->addText('command', 'Příkaz');
        $form->addSubmit('send', 'Zadat');
        $form->onSuccess[] = $this->commandFormSubmitted;
        return $form;
    }

    public function commandFormSubmitted(Nette\Application\UI\Form $form) {
        $command = $form->getValues()->command;
        $this->serverCmd->issueCommand($command, $this->runtimeHash);
        //TODO make it less ugly and more reliable
        usleep(500000);
        if ($this->isAjax()) {
            $this->redrawControl();
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

    public function renderStarted() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        try {
            $logModel = new LogModel($path . 'logs/');
            $this->template->logs = LogModel::makeColorful($logModel->getAll(8));
        } catch (UnexpectedValueException $e) {
            $this->template->logs = array('nic nenalezeno');
        }
    }

}
