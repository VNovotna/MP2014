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

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->serverRepo = $this->context->serverRepository;
        $this->gameUpdater = $this->context->gameUpdateModel;
    }

    public function renderDefault() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        $this->template->dowJars = array_merge($this->gameUpdater->getSnapshotsJars(), $this->gameUpdater->getStableJars());
        $this->template->avJars = $this->gameUpdater->getAvailableJars($path);
    }

}
