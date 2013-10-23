<?php

/**
 * Description of BackupPresenter
 *
 * @author viky
 */
class BackupPresenter extends SecuredPresenter {

    /** @var ServerCommander */
    private $serverCmd;

    /** @var \DB\ServerRepository */
    private $serverRepo;

    /** @var BackupModel */
    private $backupModel;

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
        $this->backupModel = $this->context->backupModel;
        if (!$this->user->isAllowed('commands', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    public function handleMakeBackup() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        if ($this->backupModel->backup($path, $this->runtimeHash)) {
            $this->flashMessage('Záloha úspěšná', 'success');
        } else {
            $this->flashMessage('Něco se nepovedlo :/', 'error');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleRestoreBackup($file) {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        if ($this->backupModel->restore($path, $file, $this->runtimeHash)) {
            sleep(10);
            $this->flashMessage('Obnova úspěšná. Nebo až se to dopíše...', 'error');
        } else {
            $this->flashMessage('Něco se nepovedlo :/ ' . $file, 'error');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeleteBackup($file) {
        if ($this->backupModel->removeFile($this->serverRepo->getPath($this->selectedServerId), $file)) {
            $this->flashMessage('Záloha ' . $file . ' odstraněna', 'success');
        } else {
            $this->flashMessage('Něco se nepovedlo :/ ', 'error');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function renderDefault() {
        $path = $this->serverRepo->getRunParams($this->selectedServerId)->path;
        $this->template->backups = $this->backupModel->getBackups($path);
    }

}