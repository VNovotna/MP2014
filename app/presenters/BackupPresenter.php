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

    /** @var string */
    private $path;

    protected function startup() {
        parent::startup();
        $this->serverCmd = $this->context->serverCommander;
        $this->serverRepo = $this->context->serverRepository;
        $this->backupModel = $this->context->backupModel;
        $this->path = $this->serverRepo->getPath($this->selectedServerId);
        if (!$this->user->isAllowed('commands', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentLoadBackup() {
        $form = new \Nette\Application\UI\Form();
        $form->addUpload('upload', 'Zip archiv se zálohou:')
                ->addRule(\Nette\Application\UI\Form::MIME_TYPE, "Soubor musí být zip archiv", "application/zip");
        $form->addSubmit('send', 'Nahrát');
        $form->onSuccess[] = $this->loadBackupSubmitted;
        $form->onError[] = function($form) {
            foreach ($form->errors as $er) {
                $form->getPresenter()->flashMessage($er, 'error');
            }
            $form->cleanErrors();
        };
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function loadBackupSubmitted($form) {
        $values = $form->getValues();
        $archive = $values->upload;
        if ($archive->isOK()) {
            $archive->move($this->path . 'backups/' . $archive->getName());
            $this->flashMessage('Archiv nahrán', 'success');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
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
        $executable = $this->serverRepo->getRunParams($this->selectedServerId)->executable;
        if ($this->backupModel->restore($this->path, $file, $this->runtimeHash, $executable)) {
            $this->flashMessage('Obnova úspěšná.', 'success');
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
        if ($this->backupModel->removeFile($this->path, $file)) {
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

    public function handleDownload($file) {
        $fr = new \Nette\Application\Responses\FileResponse($this->path . 'backups/' . $file, $file, "application/zip");
        $this->sendResponse($fr);
    }

    public function renderDefault() {
        $this->template->backups = $this->backupModel->getBackups($this->path);
    }

}
