<?php

/**
 * VersionManagerPresenter manages versions of minecraft server
 *
 * @author viky
 */
class VersionManagerPresenter extends SecuredPresenter {

    /** @var \DB\ServerRepository */
    private $serverRepo;

    /** @var GameUpdateModel */
    private $gameUpdater;

    /** @var ServerCommander */
    private $serverCmd;

    /** @var string */
    private $path;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->serverRepo = $this->context->serverRepository;
        $this->gameUpdater = $this->context->gameUpdateModel;
        $this->serverCmd = $this->context->serverCommander;
        $this->path = $this->serverRepo->getPath($this->selectedServerId);
    }

    /**
     * @param string $filename
     */
    public function handleUseFile($filename) {
        if ($this->serverCmd->isServerRunning($this->runtimeHash)) {
            $this->serverCmd->stopServer($this->runtimeHash);
            $this->flashMessage('Server byl vypnut.', 'info');
        }
        $this->serverRepo->setExecutable($this->selectedServerId, $filename);
        $this->flashMessage('Verze nastavena úspěšně.', 'success');
        if ($this->isAjax()) {
            $this->redrawControl();
            $this->redrawControl('runIcon');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @param string $file
     */
    public function handleDeleteFile($file) {
        if (unlink($this->path . $file)) {
            $this->flashMessage('Smazáno', 'success');
        } else {
            $this->flashMessage('Unlink failed', 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleShowUpdates() {
        $this->template->showUpdates = TRUE;
        $this->redrawControl('UpdateList');
    }

    protected function createComponentUpdates() {
        return new Updates($this->gameUpdater, $this->path);
    }

    public function renderDefault() {
        try {
            $this->template->avJars = $this->gameUpdater->getAvailableJars($this->path);
        } catch (UnexpectedValueException $e) {
            $this->flashMessage('Máte špatně nastavenou cestu!', 'error');
            $this->template->avJars = array();
        }
        $exec = $this->serverRepo->getRunParams($this->selectedServerId)->executable;
        $this->template->active = $this->gameUpdater->getVersionFromFileName($exec);
    }

}
