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

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->serverRepo = $this->context->serverRepository;
        $this->gameUpdater = $this->context->gameUpdateModel;
        $this->serverCmd = $this->context->serverCommander;
    }

    /**
     * @param string $filename
     */
    public function handleUseFile($filename) {
        if($this->serverCmd->isServerRunning($this->runtimeHash)){
            $this->serverCmd->stopServer($this->runtimeHash);
            $this->flashMessage('Server byl vypnut.','info');    
        }
        $this->serverRepo->setExecutable($this->selectedServerId, $filename);
        $this->flashMessage($filename,'success');
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function renderDefault() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        $this->template->dowJars = array_merge($this->gameUpdater->getSnapshotsJars(), $this->gameUpdater->getStableJars());
        $this->template->avJars = $this->gameUpdater->getAvailableJars($path);
        $exec = $this->serverRepo->getRunParams($this->selectedServerId)->executable;
        $this->template->active = $this->gameUpdater->getVersionFromFileName($exec);
    }

}
